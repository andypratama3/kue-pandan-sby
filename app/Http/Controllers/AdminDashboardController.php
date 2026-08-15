<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Region;
use App\Models\User;
use App\Support\RegionContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    public function index(Request $request, string $region)
    {
        $admin = Auth::user();

        if (! $admin->hasRole('admin') && ! $admin->hasRole('owner')) {
            abort(403, 'AKSES DITOLAK');
        }

        // ===== ISOLASI CABANG =====
        // Admin selalu terikat region-nya sendiri (parameter URL diabaikan agar
        // tidak bisa membuka cabang lain). Owner mengikuti cabang yang dipilih
        // via session (branch switcher).
        $regionId = RegionContext::regionId();
        if (! $regionId) {
            abort(403, 'Cabang tidak valid.');
        }
        $regionModel = Region::find($regionId);

        // ===== TRACK VISITOR (OPTIMIZED) =====
        $allowedVisitFilters = ['daily', 'weekly', 'monthly', 'last_month', 'last_7_days'];
        $visitFilter = $request->input('visit_filter', 'last_7_days');
        if (! in_array($visitFilter, $allowedVisitFilters, true)) {
            $visitFilter = 'last_7_days';
        }
        $year = now()->year;

        $activeMonth = (int) $request->input('month', now()->month);
        if ($activeMonth < 1 || $activeMonth > 12) {
            $activeMonth = now()->month;
        }

        // ===== GRAFIK PENJUALAN =====
        $allowedFilters = ['daily', 'weekly', 'monthly', 'last_month', 'last_7_days'];
        $filter = $request->input('filter', 'last_7_days');
        if (! in_array($filter, $allowedFilters, true)) {
            $filter = 'last_7_days';
        }

        $visitChartLabels = [];
        $visitChartData = [];
        $visitDateRangeText = '';

        // Optimasi: Cache visit data untuk mengurangi query berulang
        $cacheKeyVisit = "visit_stats_{$regionId}_{$visitFilter}_{$activeMonth}";
        $visitStats = cache()->remember($cacheKeyVisit, 300, function() use ($visitFilter, $year, $activeMonth, $regionId) {
            $labels = [];
            $data = [];
            $rangeText = '';

            switch ($visitFilter) {
                case 'daily':
                    $daysInMonth = Carbon::create($year, $activeMonth)->daysInMonth;
                    $dates = collect(range(1, $daysInMonth))->map(function($day) use ($year, $activeMonth) {
                        return Carbon::createFromDate($year, $activeMonth, $day)->format('Y-m-d');
                    });

                    $visitCounts = DB::table('visit_logs')
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $activeMonth)
                        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->groupBy('date')
                        ->pluck('count', 'date');

                    foreach ($dates as $dateStr) {
                        $labels[] = Carbon::parse($dateStr)->format('d');
                        $data[] = $visitCounts->get($dateStr, 0);
                    }
                    $rangeText = Carbon::create($year, $activeMonth)->isoFormat('MMMM YYYY');
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

                        $labels[] = 'Minggu '.$week;
                        $data[] = DB::table('visit_logs')
                            ->whereBetween('created_at', [$startDate, $weekEnd])
                            ->count();

                        $startDate = $weekEnd->addDay();
                        $week++;
                    }
                    $rangeText = Carbon::now()->isoFormat('MMMM YYYY');
                    break;

                case 'monthly':
                    $visitCounts = DB::table('visit_logs')
                        ->whereYear('created_at', now()->year)
                        ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                        ->groupBy('month')
                        ->pluck('count', 'month');

                    for ($month = 1; $month <= 12; $month++) {
                        $date = Carbon::createFromDate(now()->year, $month, 1);
                        $labels[] = $date->isoFormat('MMM');
                        $data[] = $visitCounts->get($month, 0);
                    }
                    $rangeText = now()->year;
                    break;

                case 'last_month':
                    $lastMonth = Carbon::now()->subMonth();
                    $daysInMonth = $lastMonth->daysInMonth;

                    $visitCounts = DB::table('visit_logs')
                        ->whereYear('created_at', $lastMonth->year)
                        ->whereMonth('created_at', $lastMonth->month)
                        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->groupBy('date')
                        ->pluck('count', 'date');

                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $date = Carbon::createFromDate($lastMonth->year, $lastMonth->month, $day);
                        $labels[] = $date->format('d');
                        $data[] = $visitCounts->get($date->format('Y-m-d'), 0);
                    }
                    $rangeText = $lastMonth->isoFormat('MMMM YYYY');
                    break;

                case 'last_7_days':
                default:
                    $dates = collect(range(6, 0))->map(function($i) {
                        return Carbon::today()->subDays($i);
                    });

                    $visitCounts = DB::table('visit_logs')
                        ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
                        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->groupBy('date')
                        ->pluck('count', 'date');

                    foreach ($dates as $date) {
                        $labels[] = $date->format('d M');
                        $data[] = $visitCounts->get($date->format('Y-m-d'), 0);
                    }

                    $start = Carbon::today()->subDays(6);
                    $end = Carbon::today();
                    $rangeText = $start->isoFormat('D MMM').' - '.$end->isoFormat('D MMM');
                    break;
            }

            return ['labels' => $labels, 'data' => $data, 'rangeText' => $rangeText];
        });

        $visitChartLabels = $visitStats['labels'];
        $visitChartData = $visitStats['data'];
        $visitDateRangeText = $visitStats['rangeText'];
        $totalVisitsInRange = array_sum($visitChartData);

        // ===== TRACK VISITOR =====

        // --- DATA UTAMA UNTUK HARI INI (OPTIMIZED WITH SINGLE QUERY) ---
        // Optimasi: Gunakan single query untuk mengambil semua data hari ini
        $todayStats = cache()->remember("dashboard_today_stats_{$regionId}", 60, function() use ($regionId) {
            $today = Carbon::today();
            
            // Income hari ini (tanpa retur aktif)
            $incomeToday = Order::where('region_id', $regionId)
                ->whereDate('created_at', $today)
                ->whereDoesntHave('returns', function ($query) {
                    $query->where('status', '!=', 'ditolak');
                })
                ->sum('total_amount');

            $totalSalesToday = Order::where('region_id', $regionId)
                ->whereDate('created_at', $today)
                ->count();

            // Income kemarin
            $incomeYesterday = Order::where('region_id', $regionId)
                ->whereDate('created_at', Carbon::yesterday())
                ->whereDoesntHave('returns', function ($query) {
                    $query->where('status', '!=', 'ditolak');
                })
                ->sum('total_amount');

            return [
                'income_today' => $incomeToday,
                'total_sales_today' => $totalSalesToday,
                'income_yesterday' => $incomeYesterday,
            ];
        });

        $incomeToday = $todayStats['income_today'];
        $totalSalesToday = $todayStats['total_sales_today'];
        $incomeYesterday = $todayStats['income_yesterday'];

        // DATA AVG (OPTIMIZED)
        $thisMonth = now();

        // Optimasi: Gunakan cache untuk perhitungan bulanan
        $monthlyStats = cache()->remember("dashboard_monthly_stats_{$regionId}", 300, function() use ($regionId) {
            $thisMonth = now();
            $lastMonth = now()->subMonth();

            $totalSalesThisMonth = Order::where('region_id', $regionId)
                ->whereYear('created_at', $thisMonth->year)
                ->whereMonth('created_at', $thisMonth->month)
                ->count();

            $totalSalesLastMonth = Order::where('region_id', $regionId)
                ->whereYear('created_at', $lastMonth->year)
                ->whereMonth('created_at', $lastMonth->month)
                ->count();

            // Avg sales per month tahun ini
            $avgSalesThisYear = Order::where('region_id', $regionId)
                ->whereYear('created_at', $thisMonth->year)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->get()
                ->avg('total') ?? 0;

            return [
                'total_sales_this_month' => $totalSalesThisMonth,
                'total_sales_last_month' => $totalSalesLastMonth,
                'avg_sales_this_year' => $avgSalesThisYear,
                'days_this_month' => $thisMonth->daysInMonth,
                'days_last_month' => $lastMonth->daysInMonth,
            ];
        });

        $totalSalesThisMonth = $monthlyStats['total_sales_this_month'];
        $totalSalesLastMonth = $monthlyStats['total_sales_last_month'];
        $avgSalesPerMonth = $monthlyStats['avg_sales_this_year'] ?? 0;
        $daysThisMonth = $monthlyStats['days_this_month'];
        $daysLastMonth = $monthlyStats['days_last_month'];

        $avgSalesThisMonth = $daysThisMonth > 0 ? $totalSalesThisMonth / $daysThisMonth : 0;
        $avgSalesLastMonth = $daysLastMonth > 0 ? $totalSalesLastMonth / $daysLastMonth : 0;

        // Customer stats (optimized)
        $customerStats = cache()->remember("dashboard_customer_stats_{$regionId}", 300, function() use ($regionId) {
            $totalCustomers = Customer::where('region_id', $regionId)->count();
            $newToday = Customer::where('region_id', $regionId)
                ->whereDate('created_at', Carbon::today())
                ->count();
            $newYesterday = Customer::where('region_id', $regionId)
                ->whereDate('created_at', Carbon::yesterday())
                ->count();
            $newThisWeek = Customer::where('region_id', $regionId)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count();
            $newLastWeek = Customer::where('region_id', $regionId)
                ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
                ->count();

            return [
                'total' => $totalCustomers,
                'new_today' => $newToday,
                'new_yesterday' => $newYesterday,
                'new_this_week' => $newThisWeek,
                'new_last_week' => $newLastWeek,
            ];
        });

        $totalCustomersInRegion = $customerStats['total'];
        $newCustomersToday = $customerStats['new_today'];
        $newCustomersYesterday = $customerStats['new_yesterday'];
        $newCustomersThisWeek = $customerStats['new_this_week'];
        $newCustomersLastWeek = $customerStats['new_last_week'];

        // --- LOGIKA BARU UNTUK PERHITUNGAN PERSENTASE (OPTIMIZED) ---

        // 1. Income: Hari ini vs Kemarin
        if ($incomeYesterday > 0) {
            $incomePercentageChange = (($incomeToday - $incomeYesterday) / $incomeYesterday) * 100;
        } elseif ($incomeToday > 0) {
            $incomePercentageChange = 100;
        } else {
            $incomePercentageChange = 0;
        }

        // 2. Total Sales: Bulan ini vs Bulan lalu
        if ($totalSalesLastMonth > 0) {
            $salesPercentageChange = (($totalSalesThisMonth - $totalSalesLastMonth) / $totalSalesLastMonth) * 100;
        } elseif ($totalSalesThisMonth > 0) {
            $salesPercentageChange = 100;
        } else {
            $salesPercentageChange = 0;
        }

        // 3. Avg Sales: bulan ini vs bulan lalu
        if ($avgSalesLastMonth > 0) {
            $avgSalesPercentageChange = (($avgSalesThisMonth - $avgSalesLastMonth) / $avgSalesLastMonth) * 100;
        } elseif ($avgSalesThisMonth > 0) {
            $avgSalesPercentageChange = 100;
        } else {
            $avgSalesPercentageChange = 0;
        }

        // 4. Customer (Region): Minggu ini vs Minggu lalu
        if ($newCustomersLastWeek > 0) {
            $customerPercentageChange = (($newCustomersThisWeek - $newCustomersLastWeek) / $newCustomersLastWeek) * 100;
        } elseif ($newCustomersThisWeek > 0) {
            $customerPercentageChange = 100;
        } else {
            $customerPercentageChange = 0;
        }

        // 5. New Customer: Hari ini vs Kemarin
        if ($newCustomersYesterday > 0) {
            $newCustomerPercentageChange = (($newCustomersToday - $newCustomersYesterday) / $newCustomersYesterday) * 100;
        } elseif ($newCustomersToday > 0) {
            $newCustomerPercentageChange = 100;
        } else {
            $newCustomerPercentageChange = 0;
        }

        // Pending orders notification
        $pendingOrders = Order::where('region_id', $regionId)
            ->whereIn('status', ['menunggu_verifikasi_admin', 'selesai'])
            ->count();

        $couriers = User::role('kurir')->where('region_id', $regionId)->latest()->paginate(5, ['*'], 'couriers_page');

        // Statistik katalog region: produk, varian aktif, kategori.
        $catalogStats = [
            'products' => Product::where('region_id', $regionId)->count(),
            'variants' => ProductVariant::whereHas('product', fn ($q) => $q->where('region_id', $regionId))
                ->where('is_active', true)
                ->count(),
            'categories' => Category::count(),
        ];

        // --- LOGIKA BARU UNTUK GRAFIK PENJUALAN ADMIN ---
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
                    if ($weekEndDate->gt($endDate)) {
                        $weekEndDate = $endDate;
                    }

                    $chartLabels[] = 'Minggu '.$weekNumber;
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
                $endDate = $lastMonth->copy()->endOfMonth();

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
                $dateRangeText = $startDate->isoFormat('D MMM').' - '.$endDate->isoFormat('D MMM');
                break;
        }

        $totalOrdersInRange = array_sum($chartDataTotal);
        $totalVerifiedInRange = array_sum($chartDataVerified);
        $totalVerifiedWithReturnInRange = array_sum($chartDataVerifiedWithReturn);

        // ===== RINGKASAN PER CABANG (KHUSUS OWNER) =====
        $branchSummary = null;
        if ($admin->hasRole('owner')) {
            $branchSummary = Region::where('is_active', true)->orderBy('id')->get()->map(function ($r) {
                $today = Carbon::today();

                return [
                    'region' => $r,
                    'income_today' => (int) Order::where('region_id', $r->id)
                        ->whereDate('created_at', $today)
                        ->whereDoesntHave('returns', fn ($q) => $q->where('status', '!=', 'ditolak'))
                        ->sum('total_amount'),
                    'orders_today' => Order::where('region_id', $r->id)->whereDate('created_at', $today)->count(),
                    'pending_verify' => Order::where('region_id', $r->id)
                        ->whereIn('status', ['selesai', 'menunggu_verifikasi_admin'])
                        ->count(),
                    'customers' => Customer::where('region_id', $r->id)->count(),
                    'couriers' => User::role('kurir')->where('region_id', $r->id)->count(),
                ];
            })->values();
        }

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

            // CABANG
            'regionModel',
            'branchSummary',

            // KATALOG
            'catalogStats',
            
            // NOTIFICATION
            'pendingOrders',
        ));
    }

    /**
     * Ganti cabang aktif (khusus owner). Pilihan disimpan di session.
     */
    public function switchRegion(string $region)
    {
        $user = Auth::user();
        if (! $user->hasRole('owner')) {
            abort(403, 'AKSES DITOLAK');
        }

        $regionModel = Region::where('is_active', true)->where('slug', $region)->firstOrFail();
        session(['selected_region_id' => $regionModel->id]);

        return redirect()->route('admin.dashboard', ['region' => $regionModel->slug]);
    }

    public function profile()
    {
        $admin = Auth::user();
        if (! $admin->hasRole('admin') && ! $admin->hasRole('owner')) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('dashboard.admin.profile.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('admin') && ! $user->hasRole('owner')) {
            abort(403, 'AKSES DITOLAK');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'not_regex:/[\r\n]/', Rule::unique('users')->ignore($user->id)],
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
        if (! $user->hasRole('admin') && ! $user->hasRole('owner')) {
            abort(403, 'AKSES DITOLAK');
        }
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
    }
}
