<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Region;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KurirCustomerController extends Controller
{
    /**
     * Format nomor telepon ke standar +62.
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        $cleanedPhone = preg_replace('/[^\d+]/', '', $phone);
        $baseNumber = preg_replace('/^(0|\+?62)/', '', $cleanedPhone);
        return '62' . $baseNumber;
    }

    /**
     * Menampilkan halaman manajemen customer.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userRegionId = Auth::user()->region_id;
        $customersQuery = Customer::where('region_id', $userRegionId)->with('region');

        if ($search) {
            $customersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('phone', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%')
                      ->orWhere('note', 'like', '%' . $search . '%');
            });
        }

        $customers = $customersQuery->latest()->paginate(10);
        return view('dashboard.kurir.customers.index', compact('customers'));
    }

    /**
     * Menyimpan data customer baru.
     */
    public function store(Request $request)
    {
        $formattedPhone = $this->formatPhoneNumber($request->phone);
        $request->merge(['phone' => $formattedPhone]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => [
                'required',
                'string',
                Rule::unique('customers')->where(function ($query) use ($formattedPhone) {
                    return $query->where('phone', $formattedPhone)
                                 ->where('region_id', Auth::user()->region_id);
                }),
            ],
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string',
        ], [
            'address.unique' => 'Customer dengan alamat dan nomor telepon ini sudah terdaftar.'
        ]);

        if ($validator->fails()) {
            // Jika validasi gagal, kirim pesan error ke session untuk ditampilkan sebagai toast
            return redirect()->route('kurir.customers.index')
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first('address'));
        }

        $customer = Customer::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $formattedPhone,
            'note' => $request->note,
            'region_id' => Auth::user()->region_id,
        ]);

        return redirect()->route('kurir.customers.index')->with('success', 'Customer "' . $customer->name . '" berhasil ditambahkan.');
    }

    /**
     * Update data customer
     */
    public function update(Request $request, Customer $customer)
    {
        $formattedPhone = $this->formatPhoneNumber($request->phone);
        $request->merge(['phone' => $formattedPhone]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => [
                'required',
                'string',
                Rule::unique('customers')->where(function ($query) use ($formattedPhone) {
                    return $query->where('phone', $formattedPhone)
                                 ->where('region_id', Auth::user()->region_id);
                })->ignore($customer->id),
            ],
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string',
        ], [
            'address.unique' => 'Customer dengan alamat dan nomor telepon ini sudah terdaftar.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('kurir.customers.index')
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first('address'));
        }

        $customer->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $formattedPhone,
            'note' => $request->note
        ]);

        return redirect()->route('kurir.customers.index')->with('success', 'Data customer "' . $customer->name . '" berhasil diperbarui.');
    }

    /**
     * Update note customer
     */
    public function updateNote(Request $request, Customer $customer)
    {
        $request->validate([
            'note' => 'nullable|string',
        ]);

        $customer->update(['note' => $request->note]);

        return redirect()->route('kurir.customers.index')->with('success', 'Catatan untuk "' . $customer->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus data customer
     */
    public function destroy(Customer $customer)
    {
        $customerName = $customer->name;
        $customer->delete();
        return redirect()->route('kurir.customers.index')->with('success', 'Customer "' . $customerName . '" berhasil dihapus.');
    }
}
