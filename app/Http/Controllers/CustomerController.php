<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Order;
use App\Models\User;
use App\Support\RegionContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        return '62'.preg_replace('/^(0|\+?62)/', '', preg_replace('/[^\d]/', '', $phone));
    }

    private function isAdminLike(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('owner');
    }

    // Download rekap order customer (PDF)
    public function downloadRekap(Request $request, Customer $customer)
    {
        $user = Auth::user();
        if (! $this->isAdminLike($user) || $customer->region_id !== RegionContext::regionId()) {
            abort(403, 'AKSES DITOLAK');
        }

        // Ambil rentang tanggal
        $daterange = $request->input('daterange');
        if (! $daterange) {
            return back()->with('error', 'Rentang tanggal harus diisi.');
        }
        [$start, $end] = array_map('trim', explode(' - ', $daterange));
        $startDate = date('Y-m-d 00:00:00', strtotime($start));
        $endDate = date('Y-m-d 23:59:59', strtotime($end));

        // Ambil order customer pada rentang tanggal

        $orders = Order::where('customer_id', $customer->id)
            ->where('status', 'diverifikasi_admin')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product', 'items.variant', 'returns.returnedProducts.product', 'returns.returnedProducts.variant'])
            ->get();

        // Susun data rekap per order
        $rekapPerOrder = [];
        $produkTotal = [];
        foreach ($orders as $order) {
            $produkDipesan = [];
            // Dipesan
            foreach ($order->items as $item) {
                $key = $item->product_id.'-'.($item->variant_id ?? '');
                $produkDipesan[$key] = [
                    'product_name' => $item->product->name ?? $item->product_name,
                    'variant_name' => $item->variant->name ?? $item->variant_name ?? null,
                    'dipesan' => $item->quantity,
                    'retur' => 0,
                ];
                // Akumulasi total
                if (! isset($produkTotal[$key])) {
                    $produkTotal[$key] = [
                        'product_name' => $produkDipesan[$key]['product_name'],
                        'variant_name' => $produkDipesan[$key]['variant_name'],
                        'dipesan' => 0,
                        'retur' => 0,
                    ];
                }
                $produkTotal[$key]['dipesan'] += $item->quantity;
            }
            // Retur (ambil dari returns yang status != 'ditolak')
            foreach ($order->returns as $retur) {
                if ($retur->status === 'ditolak') {
                    continue;
                }
                foreach ($retur->returnedProducts as $returItem) {
                    $key = $returItem->product_id.'-'.($returItem->product_variant_id ?? '');
                    if (! isset($produkDipesan[$key])) {
                        $produkDipesan[$key] = [
                            'product_name' => $returItem->product->name ?? '-',
                            'variant_name' => $returItem->variant->name ?? null,
                            'dipesan' => 0,
                            'retur' => 0,
                        ];
                    }
                    $produkDipesan[$key]['retur'] += $returItem->quantity;
                    // Akumulasi total
                    if (! isset($produkTotal[$key])) {
                        $produkTotal[$key] = [
                            'product_name' => $produkDipesan[$key]['product_name'],
                            'variant_name' => $produkDipesan[$key]['variant_name'],
                            'dipesan' => 0,
                            'retur' => 0,
                        ];
                    }
                    $produkTotal[$key]['retur'] += $returItem->quantity;
                }
            }
            // Hitung selisih per order
            foreach ($produkDipesan as &$row) {
                $row['selisih'] = $row['dipesan'] - $row['retur'];
            }
            unset($row);
            $rekapPerOrder[] = [
                'order' => $order,
                'produk' => $produkDipesan,
            ];
        }
        // Hitung selisih total
        foreach ($produkTotal as &$row) {
            $row['selisih'] = $row['dipesan'] - $row['retur'];
        }
        unset($row);

        // Generate PDF
        $pdf = Pdf::loadView('dashboard.admin.customers.rekap_pdf', [
            'customer' => $customer,
            'produkTotal' => $produkTotal,
            'rekapPerOrder' => $rekapPerOrder,
            'start' => $start,
            'end' => $end,
        ]);
        $filename = 'Rekap_Order_'.$customer->name.'_'.$start.'_to_'.$end.'.pdf';

        return $pdf->download($filename);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Mulai query dengan filter region dasar
        $customersQuery = Customer::where('region_id', RegionContext::regionId());

        // JIKA USER ADALAH KURIR, tambahkan filter tambahan agar hanya melihat data yang diinputnya sendiri
        if ($user->hasRole('kurir')) {
            $customersQuery->where('added_by_user_id', $user->id);
        }
        // Admin akan tetap melihat semua customer di regionnya.

        // Terapkan filter pencarian
        $customersQuery->when($search, function ($query, $searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('company_name', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%")
                    ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        });

        $customers = $customersQuery->latest()->paginate(10);
        $customerCategories = CustomerCategory::all();
        $couriers = [];
        if ($this->isAdminLike($user)) {
            $couriers = User::role('kurir')->where('region_id', RegionContext::regionId())->get();
        }

        if ($request->ajax()) {
            $isAdmin = $this->isAdminLike($user);
            $viewPath = $isAdmin ? 'dashboard.admin.customers.' : 'dashboard.kurir.customers.';

            // PERBAIKAN 1: Tambahkan $couriers ke $viewData
            $viewData = compact('customers', 'customerCategories', 'couriers');

            $desktopHtml = view($viewPath.'_table_rows', $viewData)->render();

            // PERBAIKAN 2: Tentukan path modal secara dinamis berdasarkan role
            $modalViewPath = $isAdmin ? 'dashboard.admin.customers._modals' : 'dashboard.kurir.customers._modals';
            $modalsHtml = view($modalViewPath, $viewData)->render();

            $response = ['desktop_html' => $desktopHtml, 'modals_html' => $modalsHtml];

            if (! $isAdmin) {
                $response['mobile_html'] = view($viewPath.'_card_view', $viewData)->render();
            }

            return response()->json($response);
        }

        $view = $this->isAdminLike($user) ? 'dashboard.admin.customers.index' : 'dashboard.kurir.customers.index';

        return view($view, compact('customers', 'customerCategories', 'couriers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $formattedPhone = $this->formatPhoneNumber($request->phone);
        $request->merge(['phone' => $formattedPhone]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address' => 'required|string',
            'landmark' => 'nullable|string|max:255',
            'phone' => ['required', 'string', 'max:20'],
            'customer_category_id' => 'nullable|exists:customer_categories,id',
            'opening_hours' => 'nullable|string|max:255',
            'payment_type' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ], ['phone.unique' => 'Nomor telepon ini sudah terdaftar di region Anda.']);

        $routeName = $this->isAdminLike($user) ? 'admin.customers.index' : 'kurir.customers.index';
        if ($validator->fails()) {
            return redirect()->route($routeName)->withErrors($validator)->withInput()->with('error', $validator->errors()->first());
        }

        Customer::create(array_merge($request->all(), [
            'region_id' => RegionContext::regionId(),
            'added_by_user_id' => $user->id, // ID kurir yang menginput otomatis tersimpan
        ]));

        return redirect()->route($routeName)->with('success', 'Customer "'.$request->name.'" berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();

        // PERIKSA KEPEMILIKAN DATA UNTUK KURIR
        if ($user->hasRole('kurir') && $customer->added_by_user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin untuk mengubah data customer ini.');
        }
        // Admin hanya diperiksa regionnya
        if (($user->hasRole('admin') || $user->hasRole('owner')) && $customer->region_id !== RegionContext::regionId()) {
            abort(403, 'AKSES DITOLAK');
        }

        $formattedPhone = $this->formatPhoneNumber($request->phone);
        $request->merge(['phone' => $formattedPhone]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'landmark' => 'nullable|string|max:255',
            'phone' => ['required', 'string', 'max:20'],
            'customer_category_id' => 'required|exists:customer_categories,id',
            'opening_hours' => 'required|string|max:255',
            'payment_type' => 'required|string|max:255',
            'note' => 'nullable|string',
        ], ['phone.unique' => 'Nomor telepon ini sudah terdaftar untuk customer lain.']);

        $routeName = $this->isAdminLike($user) ? 'admin.customers.index' : 'kurir.customers.index';
        if ($validator->fails()) {
            return redirect()->route($routeName)->withErrors($validator)->withInput()->with('error', 'Gagal memperbarui data. '.$validator->errors()->first());
        }

        $customer->update($request->all());

        return redirect()->route($routeName)->with('success', 'Data customer "'.$customer->name.'" berhasil diperbarui.');
    }

    public function updateNote(Request $request, Customer $customer)
    {
        $user = Auth::user();
        // PERIKSA KEPEMILIKAN DATA UNTUK KURIR
        if ($user->hasRole('kurir') && $customer->added_by_user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK');
        }
        if (($user->hasRole('admin') || $user->hasRole('owner')) && $customer->region_id !== RegionContext::regionId()) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate(['note' => 'nullable|string']);
        $customer->update(['note' => $request->note]);

        $routeName = $this->isAdminLike($user) ? 'admin.customers.index' : 'kurir.customers.index';

        return redirect()->route($routeName)->with('success', 'Catatan untuk "'.$customer->name.'" berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        // PERIKSA KEPEMILIKAN DATA UNTUK KURIR
        if ($user->hasRole('kurir') && $customer->added_by_user_id !== $user->id) {
            abort(403, 'AKSES DITOLAK');
        }
        if (($user->hasRole('admin') || $user->hasRole('owner')) && $customer->region_id !== RegionContext::regionId()) {
            abort(403, 'AKSES DITOLAK');
        }

        $customerName = $customer->name;
        $customer->delete();

        $routeName = $this->isAdminLike($user) ? 'admin.customers.index' : 'kurir.customers.index';

        return redirect()->route($routeName)->with('success', 'Customer "'.$customerName.'" berhasil dihapus.');
    }

    public function toggleFlag(Request $request, Customer $customer)
    {
        $user = Auth::user();
        // Fitur ini hanya untuk Admin/Owner
        if (! $this->isAdminLike($user) || $customer->region_id !== RegionContext::regionId()) {
            abort(403, 'AKSES DITOLAK');
        }

        $customer->is_flagged = ! $customer->is_flagged;
        $customer->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_flagged' => $customer->is_flagged,
                'message' => 'Status flag customer berhasil diubah.',
            ]);
        }

        $status = $customer->is_flagged ? 'ditandai' : 'dihilangkan tandanya';

        return redirect()->route('admin.customers.index')->with('success', 'Customer "'.$customer->name.'" berhasil '.$status.'.');
    }
}
