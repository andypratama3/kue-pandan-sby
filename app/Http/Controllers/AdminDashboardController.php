<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Region;
use App\Models\OrderReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request, string $region)
    {
        $admin = Auth::user();

        if (!$admin->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }

        // ===== TRACK VISITOR =====
        $visitFilter = $request->input('visit_filter', 'last_7_days');
        $year = now()->year;

        $activeMonth = $monthFilter ?? now()->month;

        $visitChartLabels = [];
        $visitChartData = [];
        $visitDateRangeText = '';



        switch ($visitFilter) {

            case 'daily':
                $daysInMonth = Carbon::create($year, $activeMonth)->daysInMonth;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate($year, $activeMonth, $day);

                    $visitChartLabels[] = $date->format('d');
                    $visitChartData[] = DB::table('visit_logs')
                        ->whereDate('created_at', $date)
                        ->count();
                }

                $visitDateRangeText = Carbon::create($year, $activeMonth)->isoFormat('MMMM YYYY');
                break;

            case 'weekly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $week = 1;

                while ($startDate->lte($endDate)) {
                    $weekEnd = $startDate->copy()->endOfWeek(Carbon::SATURDAY);
                    if ($weekEnd->gt($endDate)) {
                        $weekEnd = $endDate;
                    }

                    $visitChartLabels[] = 'Minggu ' . $week;
                    $visitChartData[] = DB::table('visit_logs')
                        ->whereBetween('created_at', [$startDate, $weekEnd])
                        ->count();

                    $startDate = $weekEnd->addDay();
                    $week++;
                }

                $visitDateRangeText = Carbon::now()->isoFormat('MMMM YYYY');
                break;

            case 'monthly':
                for ($month = 1; $month <= 12; $month++) {
                    $date = Carbon::createFromDate(now()->year, $month, 1);

                    $visitChartLabels[] = $date->isoFormat('MMM');
                    $visitChartData[] = DB::table('visit_logs')
                        ->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', $month)
                        ->count();
                }

                $visitDateRangeText = now()->year;
                break;

            case 'last_month':
                $lastMonth = Carbon::now()->subMonth();

                $startDate = $lastMonth->copy()->startOfMonth();
                $endDate   = $lastMonth->copy()->endOfMonth();

                $daysInMonth = $lastMonth->daysInMonth;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate(
                        $lastMonth->year,
                        $lastMonth->month,
                        $day
                    );

                    $visitChartLabels[] = $date->format('d');
                    $visitChartData[] = DB::table('visit_logs')
                        ->whereDate('created_at', $date)
                        ->count();
                }

                $visitDateRangeText = $lastMonth->isoFormat('MMMM YYYY');
                break;


            case 'last_7_days':
            default:
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);

                    $visitChartLabels[] = $date->format('d M');
                    $visitChartData[] = DB::table('visit_logs')
                        ->whereDate('created_at', $date)
                        ->count();
                }

                $start = Carbon::today()->subDays(6);
                $end = Carbon::today();
                $visitDateRangeText = $start->isoFormat('D MMM') . ' - ' . $end->isoFormat('D MMM');
                break;
        }

        $totalVisitsInRange = array_sum($visitChartData);

        // ===== TRACK VISITOR =====


        $regionModel = Region::where('slug', $region)->firstOrFail();
        $regionId = $regionModel->id;

        // --- DATA UTAMA UNTUK HARI INI ---
        $incomeToday = Order::where('region_id', $regionId)
            ->whereDate('created_at', Carbon::today())
            ->where(function ($query) {
                $query->whereDoesntHave('returns')->orWhereHas('returns', function ($subQuery) {
                    $subQuery->where('status', 'selesai');
                });
            })
            ->sum('total_amount');

        $totalSalesToday = Order::where('region_id', $regionId)->whereDate('created_at', Carbon::today())->count();


        // DATA AVG

        $thisMonth = now();

        $totalSalesThisMonth = Order::where('region_id', $regionId)
            ->whereYear('created_at', $thisMonth->year)
            ->whereMonth('created_at', $thisMonth->month)
            ->count();

        $daysThisMonth = $thisMonth->daysInMonth;

        $avgSalesThisMonth = $daysThisMonth > 0
            ? $totalSalesThisMonth / $daysThisMonth
            : 0;


        // ===== BULAN LALU =====
        $lastMonth = now()->subMonth();

        $totalSalesLastMonth = Order::where('region_id', $regionId)
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $daysLastMonth = $lastMonth->daysInMonth;

        $avgSalesLastMonth = $daysLastMonth > 0
            ? $totalSalesLastMonth / $daysLastMonth
            : 0;


        $totalCustomersInRegion = Customer::where('region_id', $regionId)->count();
        $newCustomersToday = Customer::where('region_id', $regionId)->whereDate('created_at', Carbon::today())->count();

        // --- LOGIKA BARU UNTUK PERHITUNGAN PERSENTASE ---

        // 1. Income: Hari ini vs Kemarin
        $incomeYesterday = Order::where('region_id', $regionId)
            ->whereDate('created_at', Carbon::yesterday())
            ->where(function ($query) {
                $query->whereDoesntHave('returns')->orWhereHas('returns', function ($subQuery) {
                    $subQuery->where('status', 'selesai');
                });
            })
            ->sum('total_amount');
        if ($incomeYesterday > 0) {
            $incomePercentageChange = (($incomeToday - $incomeYesterday) / $incomeYesterday) * 100;
        } elseif ($incomeToday > 0) {
            $incomePercentageChange = 100; // Jika kemarin 0 dan hari ini ada, anggap naik 100%
        } else {
            $incomePercentageChange = 0;
        }

        // 2. Total Sales: Bulan ini vs Bulan lalu
        $totalSalesThisMonth = Order::where('region_id', $regionId)->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
        $totalSalesLastMonth = Order::where('region_id', $regionId)->whereYear('created_at', Carbon::now()->subMonth()->year)->whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        if ($totalSalesLastMonth > 0) {
            $salesPercentageChange = (($totalSalesThisMonth - $totalSalesLastMonth) / $totalSalesLastMonth) * 100;
        } elseif ($totalSalesThisMonth > 0) {
            $salesPercentageChange = 100;
        } else {
            $salesPercentageChange = 0;
        }

        // 3. Total avg: bulan ini vs bulan lalu
        $avgSalesPerMonth = Order::where('region_id', $regionId)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->get()
            ->avg('total');

        $avgSalesLastYear = Order::where('region_id', $regionId)
            ->whereYear('created_at', now()->subYear()->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->get()
            ->avg('total');

        if ($avgSalesLastMonth > 0) {
            $avgSalesPercentageChange =
                (($avgSalesThisMonth - $avgSalesLastMonth) / $avgSalesLastMonth) * 100;
        } elseif ($avgSalesThisMonth > 0) {
            $avgSalesPercentageChange = 100;
        } else {
            $avgSalesPercentageChange = 0;
        }

        // 4. Customer (Region): Minggu ini vs Minggu lalu
        $newCustomersThisWeek = Customer::where('region_id', $regionId)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $newCustomersLastWeek = Customer::where('region_id', $regionId)->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->count();
        if ($newCustomersLastWeek > 0) {
            $customerPercentageChange = (($newCustomersThisWeek - $newCustomersLastWeek) / $newCustomersLastWeek) * 100;
        } elseif ($newCustomersThisWeek > 0) {
            $customerPercentageChange = 100;
        } else {
            $customerPercentageChange = 0;
        }


        // 5. New Customer: Hari ini vs Kemarin
        $newCustomersYesterday = Customer::where('region_id', $regionId)->whereDate('created_at', Carbon::yesterday())->count();
        if ($newCustomersYesterday > 0) {
            $newCustomerPercentageChange = (($newCustomersToday - $newCustomersYesterday) / $newCustomersYesterday) * 100;
        } elseif ($newCustomersToday > 0) {
            $newCustomerPercentageChange = 100;
        } else {
            $newCustomerPercentageChange = 0;
        }


        $couriers = User::role('kurir')->whereHas('region', function ($query) use ($region) {
            $query->where('slug', $region);
        })->latest()->paginate(5, ['*'], 'couriers_page');

        // --- LOGIKA BARU UNTUK GRAFIK PENJUALAN ADMIN ---
        $filter = $request->input('filter', 'last_7_days');
        $chartLabels = [];
        $chartDataTotal = [];
        $chartDataVerified = [];
        $chartDataVerifiedWithReturn = [];
        $dateRangeText = '';

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        switch ($filter) {
            case 'daily':
                $daysInMonth = Carbon::now()->daysInMonth;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate($currentYear, $currentMonth, $day);
                    $chartLabels[] = $date->format('d');

                    $chartDataTotal[] = Order::where('region_id', $regionId)->whereDate('created_at', $date)->count();
                    $chartDataVerified[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereDate('updated_at', $date)->count();
                    $chartDataVerifiedWithReturn[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereHas('returns')->whereDate('updated_at', $date)->count();
                }
                $dateRangeText = Carbon::now()->isoFormat('MMMM YYYY');
                break;

            case 'weekly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $weekNumber = 1;
                while ($startDate->lte($endDate)) {
                    $weekEndDate = $startDate->copy()->endOfWeek(Carbon::SATURDAY);
                    if ($weekEndDate->gt($endDate)) $weekEndDate = $endDate;

                    $chartLabels[] = 'Minggu ' . $weekNumber;
                    $chartDataTotal[] = Order::where('region_id', $regionId)->whereBetween('created_at', [$startDate, $weekEndDate])->count();
                    $chartDataVerified[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereBetween('updated_at', [$startDate, $weekEndDate])->count();
                    $chartDataVerifiedWithReturn[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereHas('returns')->whereBetween('updated_at', [$startDate, $weekEndDate])->count();

                    $startDate = $weekEndDate->copy()->addDay();
                    $weekNumber++;
                }
                $dateRangeText = Carbon::now()->isoFormat('MMMM YYYY');
                break;
            case 'last_month':
                $lastMonth = Carbon::now()->subMonth();

                $startDate = $lastMonth->copy()->startOfMonth();
                $endDate   = $lastMonth->copy()->endOfMonth();

                $daysInMonth = $lastMonth->daysInMonth;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate(
                        $lastMonth->year,
                        $lastMonth->month,
                        $day
                    );

                    $chartLabels[] = $date->format('d');

                    $chartDataTotal[] = Order::where('region_id', $regionId)
                        ->whereDate('created_at', $date)
                        ->count();

                    $chartDataVerified[] = Order::where('region_id', $regionId)
                        ->where('status', 'diverifikasi_admin')
                        ->whereDate('updated_at', $date)
                        ->count();

                    $chartDataVerifiedWithReturn[] = Order::where('region_id', $regionId)
                        ->where('status', 'diverifikasi_admin')
                        ->whereHas('returns')
                        ->whereDate('updated_at', $date)
                        ->count();
                }

                $dateRangeText = $lastMonth->isoFormat('MMMM YYYY');
                break;

            case 'monthly':
                for ($month = 1; $month <= 12; $month++) {
                    $date = Carbon::createFromDate($currentYear, $month, 1);
                    $chartLabels[] = $date->isoFormat('MMM');

                    $chartDataTotal[] = Order::where('region_id', $regionId)->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)->count();
                    $chartDataVerified[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereYear('updated_at', $currentYear)->whereMonth('updated_at', $month)->count();
                    $chartDataVerifiedWithReturn[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereHas('returns')->whereYear('updated_at', $currentYear)->whereMonth('updated_at', $month)->count();
                }
                $dateRangeText = $currentYear;
                break;

            case 'last_7_days':
            default:
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    $chartLabels[] = $date->format('d M');

                    $chartDataTotal[] = Order::where('region_id', $regionId)->whereDate('created_at', $date)->count();
                    $chartDataVerified[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereDate('updated_at', $date)->count();
                    $chartDataVerifiedWithReturn[] = Order::where('region_id', $regionId)->where('status', 'diverifikasi_admin')->whereHas('returns')->whereDate('updated_at', $date)->count();
                }
                $endDate = Carbon::today();
                $startDate = Carbon::today()->subDays(6);
                $dateRangeText = $startDate->isoFormat('D MMM') . ' - ' . $endDate->isoFormat('D MMM');
                break;
        }

        $totalOrdersInRange = array_sum($chartDataTotal);
        $totalVerifiedInRange = array_sum($chartDataVerified);
        $totalVerifiedWithReturnInRange = array_sum($chartDataVerifiedWithReturn);

        // Kirim semua variabel ke view, termasuk variabel persentase yang baru
        return view('dashboard.admin.dashboard', compact(
            'couriers',
            'incomeToday',
            'totalSalesToday',
            'avgSalesPerMonth',
            'totalCustomersInRegion',
            'newCustomersToday',
            'incomePercentageChange',
            'avgSalesPercentageChange',
            'salesPercentageChange',
            'customerPercentageChange',
            'newCustomerPercentageChange',
            'filter',
            'chartLabels',
            'chartDataTotal',
            'chartDataVerified',
            'chartDataVerifiedWithReturn',

            // PENJUALAN
            'dateRangeText',
            'totalOrdersInRange',
            'totalVerifiedInRange',
            'totalVerifiedWithReturnInRange',

            // VISIT
            'visitFilter',
            'visitChartLabels',
            'visitChartData',
            'visitDateRangeText',
            'totalVisitsInRange',
        ));
    }

    public function profile()
    {
        $admin = Auth::user();
        if (!$admin->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }
        return view('dashboard.admin.profile.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }
        $user->save();
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'AKSES DITOLAK');
        }
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
    }
}
