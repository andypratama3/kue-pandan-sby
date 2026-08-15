@extends('layouts.argon')
@section('title', 'Edit Retur')
@section('page_title', 'Order')
@section('content')

    <div id="error" class="mb-4"></div>

    <form onsubmit="event.preventDefault(); submitReturn();">

        <h3 class="mb-4 font-bold tracking-wide text-black uppercase text-base dark:text-white dark:opacity-60">Data Customer
        </h3>

        <div class="space-y-4">
            <div>
                <label class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Nama
                    Customer</label>
                <input type="hidden" id="id" disabled>
                <input type="text" id="name"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    disabled>

            </div>

            <div>
                <label class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">No. Hp</label>
                <input type="text" id="phone"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    disabled>
            </div>

            <div>
                <label class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Alamat</label>
                <input type="text" id="address"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    disabled>
            </div>
        </div>

        <h3 class="mb-4 font-bold tracking-wide text-black uppercase text-base dark:text-white dark:opacity-60 mt-4">Produk
        </h3>

        <div class="space-y-4">
            <div id="product"></div>

            <div>
                <label class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Metode
                    Pembayaran</label>
                <select id="paymentMethod"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    disabled>
                    <option value="">- Pilih Metode Pembayaran -</option>
                    <option value="cash">Cash</option>
                    <option value="tf">Transfer</option>
                </select>
            </div>
            <div>
                <label class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Catatan</label>
                <textarea id="note" rows="4"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    disabled></textarea>
            </div>


        </div>

        <button type="submit" class="px-6 py-1 bg-brand text-white rounded hover:bg-brand-deep mt-6">
            Simpan Retur
        </button>
    </form>

    <script>
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        }

        function getID() {
            const path = window.location.pathname;
            const segments = path.split('/').filter(Boolean);
            const orderId = segments[2];

            return orderId;
        }

        function direct() {

            localStorage.setItem('toast', JSON.stringify({
                type: 'success',
                message: 'Bukti berhasil diunggah'
            }));

            window.location.href = '/kurir/pesanan';
        }

        async function loadContent() {
            try {
                const orderId = getID();

                if (!orderId) throw new Error('Order ID tidak ditemukan');

                const response = await fetch(`/kurir/pesanan/${orderId}/details`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Gagal memuat data');
                const data = await response.json();
                
                console.log(data);

                document.getElementById('id').value = data.order_return.id
                document.getElementById('name').value =
                    `${data.customer.name} ${data.customer.company_name ?? ''}`;
                document.getElementById('phone').value = data.customer.phone ?? '-';
                document.getElementById('address').value = data.customer.address ?? '-';
                document.getElementById('paymentMethod').value = data.payment_method ?? '';
                document.getElementById('note').value = data.note ?? '';

                let productHtml = '';

                data.products.forEach(product => {
                    const key = `${product.product_id}-${product.variant_id}`;
                    const returnedQty = product.returned_quantity ?? 0;

                    productHtml += `
                <div class="mb-4 p-3 border rounded dark:bg-gray-800 dark:border-gray-700">
                    <img src="${product.image_url || ''}"
                         class="w-24 mb-2">

                    <p class="font-semibold">
                        ${escapeHtml(product.name)} (${escapeHtml(product.variant_name || '')})
                    </p>

                    <p class="mt-1">
                        Awal: ${product.quantity} |
                        Retur:
                        <input
                            type="number"
                            min="0"
                            max="${product.quantity}"
                            value="${returnedQty}"
                            class="return-qty border px-2 w-20 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
                            data-product-key="${key}"
                        >
                        | Sisa: ${product.quantity - returnedQty}
                    </p>
                </div>
            `;
                });

                document.getElementById('product').innerHTML = productHtml;

            } catch (error) {
                console.error(error);
                document.getElementById('error').innerHTML = `
            <p class="text-red-600 dark:text-red-400 font-bold">${error.message}</p>
        `;
            }
        }

        /* ================= SUBMIT RETURN ================= */
        async function submitReturn() {
            try {
                const orderId = getID();

                if (!orderId) throw new Error('Order ID tidak ditemukan');

                const orderReturnId = document.getElementById('id').value;
                if (!orderReturnId) throw new Error('Order Return ID tidak ditemukan');

                const inputs = document.querySelectorAll('.return-qty');
                const returnQuantities = {};

                inputs.forEach(input => {
                    const key = input.dataset.productKey;
                    const qty = Number(input.value || 0);

                    if (!key) return;
                    if (qty > 0) {
                        returnQuantities[key] = qty;
                    }
                });

                if (Object.keys(returnQuantities).length === 0) {
                    throw new Error('Tidak ada produk yang diretur');
                }

                const reason = document.getElementById('note').value;

                const response = await fetch(
                    `/kurir/pesanan/${orderId}/request-return/edit`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({
                            order_return_id: orderReturnId,
                            return_quantities: returnQuantities,
                            reason: reason
                        })
                    }
                );

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message ?? 'Gagal menyimpan retur');
                }

                direct()

            } catch (error) {
                alert(`Gagal: ${error.message}`);
            }
        }


        /* ================= INIT ================= */
        loadContent();
    </script>

@endsection
