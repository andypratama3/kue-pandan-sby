<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Format nomor telepon ke standar 62.
     *
     * @param string|null $phone
     * @return string|null
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        $cleanedPhone = preg_replace('/[^\d]/', '', $phone);
        if (substr($cleanedPhone, 0, 1) === '0') {
            return '62' . substr($cleanedPhone, 1);
        }
        if (substr($cleanedPhone, 0, 2) === '62') {
            return $cleanedPhone;
        }
        return '62' . $cleanedPhone;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $customers = Customer::where('region_id', Auth::user()->region_id)
            ->when($search, function ($query, $searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('phone', 'like', "%{$searchTerm}%");
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.admin.customers.index', compact('customers'));
    }

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
            return redirect()->route('admin.customers.index')
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

        return redirect()->route('admin.customers.index')->with('success', 'Customer "' . $customer->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        if ($customer->region_id !== Auth::user()->region_id) {
            abort(403, 'AKSES DITOLAK');
        }

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
            return redirect()->route('admin.customers.index')
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first('address'));
        }

        $customer->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $formattedPhone,
            'note' => $request->note,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Data customer "' . $customer->name . '" berhasil diperbarui.');
    }

    public function updateNote(Request $request, Customer $customer)
    {
        if ($customer->region_id !== Auth::user()->region_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'note' => 'nullable|string',
        ]);

        $customer->update([
            'note' => $request->note,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Catatan untuk "' . $customer->name . '" berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->region_id !== Auth::user()->region_id) {
            abort(403, 'AKSES DITOLAK');
        }

        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer "' . $customerName . '" berhasil dihapus.');
    }
}
