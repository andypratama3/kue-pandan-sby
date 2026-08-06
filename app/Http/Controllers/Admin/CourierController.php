<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use App\Support\RegionContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class CourierController extends Controller
{
    /**
     * Menampilkan halaman manajemen kurir dengan data.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $couriersQuery = User::with('customers')
            ->where('region_id', RegionContext::regionId())
            ->whereHas('roles', fn ($query) => $query->where('name', 'kurir'))
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            });

        $couriers = $couriersQuery->latest()->paginate(10);

        if ($request->ajax()) {
            // $desktopHtml = view('dashboard.admin.couriers._table_rows', compact('couriers'))->render();
            // return response()->json(['desktop_html' => $desktopHtml]);

            // prepare data for the partial views
            $viewData = compact('couriers');

            $desktopHtml = view('dashboard.admin.couriers._table_rows', $viewData)->render();
            $modalsHtml = view('dashboard.admin.couriers._modals', $viewData)->render();
            $response = ['desktop_html' => $desktopHtml, 'modals_html' => $modalsHtml];

            return response()->json($response);
        }

        return view('dashboard.admin.couriers.index', compact('couriers'));
    }

    /**
     * Menyimpan kurir baru dari modal.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'not_regex:/[\r\n]/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.couriers.index')
                ->withErrors($validator, 'create')
                ->withInput()
                ->with('error', 'Gagal menambahkan kurir. '.$validator->errors()->first());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'region_id' => RegionContext::regionId(),
        ]);

        $user->assignRole('kurir');

        return redirect()->route('admin.couriers.index')->with('success', 'Kurir "'.$user->name.'" berhasil ditambahkan.');
    }

    /**
     * Pastikan target benar-benar kurir (bukan admin/owner) di region aktif.
     */
    private function assertCourierTarget(User $courier): void
    {
        if ($courier->region_id !== RegionContext::regionId() || ! $courier->hasRole('kurir')) {
            abort(403, 'AKSES DITOLAK');
        }
    }

    /**
     * Memperbarui data kurir dari modal.
     */
    public function update(Request $request, User $courier)
    {
        $this->assertCourierTarget($courier);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'not_regex:/[\r\n]/', 'unique:'.User::class.',email,'.$courier->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.couriers.index')
                ->withErrors($validator, 'edit_'.$courier->id)
                ->withInput()
                ->with('error_modal_id', 'edit-courier-modal-'.$courier->id);
        }

        $courier->name = $request->name;
        $courier->email = $request->email;

        if ($request->filled('password')) {
            $courier->password = Hash::make($request->password);
        }

        $courier->save();

        return redirect()->route('admin.couriers.index')->with('success', 'Data kurir "'.$courier->name.'" berhasil diperbarui.');
    }

    /**
     * Memperbarui catatan untuk kurir.
     */
    public function updateNote(Request $request, User $courier)
    {
        $this->assertCourierTarget($courier);

        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $courier->note = $validated['note'];
        $courier->save();

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Catatan untuk kurir "'.$courier->name.'" berhasil diperbarui.');
    }

    /**
     * Menghapus kurir.
     */
    public function destroy(User $courier)
    {
        $this->assertCourierTarget($courier);

        $courierName = $courier->name;
        try {
            $courier->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors('Kurir "'.$courierName.'" tidak dapat dihapus karena memiliki data retur. Pertahankan atau nonaktifkan akunnya.');
        }

        return redirect()->route('admin.couriers.index')->with('success', 'Kurir "'.$courierName.'" berhasil dihapus.');
    }

    /**
     * BARU: Menyediakan data performa untuk chart di modal.
     * Logika chart disalin dari dashboard kurir agar identik.
     */
    public function performanceData(Request $request, User $courier)
    {
        $this->assertCourierTarget($courier);

        $filter = $request->input('filter', 'last_7_days');
        $chartLabels = [];
        $chartData = [];
        $chartDataCompleted = [];
        $chartDataReturned = [];
        $dateRangeText = '';
        $courierId = $courier->id;

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Logika perhitungan data chart (sama persis seperti di dashboard kurir)
        switch ($filter) {
            case 'daily':
                $daysInMonth = Carbon::now()->daysInMonth;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate($currentYear, $currentMonth, $day);
                    $chartLabels[] = $date->format('d');
                    $chartData[] = Order::where('created_by_user_id', $courierId)->whereDate('created_at', $date)->count();
                    $chartDataCompleted[] = Order::where('created_by_user_id', $courierId)->whereDate('updated_at', $date)->where('status', 'selesai')->count();
                    $chartDataReturned[] = OrderReturn::whereHas('order', fn ($q) => $q->where('created_by_user_id', $courierId))->whereDate('created_at', $date)->count();
                }
                $dateRangeText = Carbon::now()->isoFormat('MMMM YYYY');
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $weekNumber = 1;
                while ($startDate->lte($endDate)) {
                    $weekEndDate = $startDate->copy()->endOfWeek(Carbon::SATURDAY);
                    if ($weekEndDate->gt($endDate)) {
                        $weekEndDate = $endDate;
                    }
                    $chartLabels[] = 'Minggu Ke-'.$weekNumber;
                    $chartData[] = Order::where('created_by_user_id', $courierId)->whereBetween('created_at', [$startDate, $weekEndDate])->count();
                    $chartDataCompleted[] = Order::where('created_by_user_id', $courierId)->whereBetween('updated_at', [$startDate, $weekEndDate])->where('status', 'selesai')->count();
                    $chartDataReturned[] = OrderReturn::whereHas('order', fn ($q) => $q->where('created_by_user_id', $courierId))->whereBetween('created_at', [$startDate, $weekEndDate])->count();
                    $startDate = $weekEndDate->copy()->addDay();
                    $weekNumber++;
                }
                $dateRangeText = Carbon::now()->isoFormat('MMMM YYYY');
                break;
            case 'monthly':
                for ($month = 1; $month <= 12; $month++) {
                    $date = Carbon::createFromDate($currentYear, $month, 1);
                    $chartLabels[] = $date->isoFormat('MMM');
                    $chartData[] = Order::where('created_by_user_id', $courierId)->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)->count();
                    $chartDataCompleted[] = Order::where('created_by_user_id', $courierId)->whereYear('updated_at', $currentYear)->whereMonth('updated_at', $month)->where('status', 'selesai')->count();
                    $chartDataReturned[] = OrderReturn::whereHas('order', fn ($q) => $q->where('created_by_user_id', $courierId))->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)->count();
                }
                $dateRangeText = $currentYear;
                break;
            case 'last_7_days':
            default:
                $endDate = Carbon::today();
                $startDate = Carbon::today()->subDays(6);
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    $chartLabels[] = $date->format('d M');
                    $chartData[] = Order::where('created_by_user_id', $courierId)->whereDate('created_at', $date)->count();
                    $chartDataCompleted[] = Order::where('created_by_user_id', $courierId)->whereDate('updated_at', $date)->where('status', 'selesai')->count();
                    $chartDataReturned[] = OrderReturn::whereHas('order', fn ($q) => $q->where('created_by_user_id', $courierId))->whereDate('created_at', $date)->count();
                }
                $dateRangeText = $startDate->isoFormat('D MMM').' - '.$endDate->isoFormat('D MMM');
                break;
        }

        return response()->json([
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartDataCompleted' => $chartDataCompleted,
            'chartDataReturned' => $chartDataReturned,
            'dateRangeText' => $dateRangeText,
            'totalOrdersInRange' => array_sum($chartData),
            'totalCompletedOrdersInRange' => array_sum($chartDataCompleted),
            'totalReturnedOrdersInRange' => array_sum($chartDataReturned),
        ]);
    }
}
