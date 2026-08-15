@extends('layouts.argon')
@section('title', 'Tambah Pesanan')
@section('page_title', 'Order')
@section('content')

    <div class="flex-auto p-4">
        {{-- FORM UTAMA UNTUK DATA PESANAN --}}
        <form class="p-0 m-0" action="{{ route('kurir.orders.checkout') }}" method="POST" id="order-form">
            @csrf {{-- Tambahkan token CSRF untuk keamanan Laravel --}}

            <div class="flex flex-col xl:flex-row -mx-7 -mt-7 xl:-mt-9">
                {{-- KOLOM KIRI: DETAIL PRODUK --}}
                <div class="order-2 w-full max-w-full px-3 mt-4 mb-12 shrink-0 xl:w-7/12 xl:flex-0 xl:order-1 xl:mb-0">
                    <div
                        class="p-3 bg-white border border-gray-200 shadow-md rounded-xl dark:bg-gray-800 dark:border-gray-700">
                        <p class="mb-4 font-bold tracking-wide text-black uppercase text-base dark:text-white dark:opacity-60">
                            <i class="fas fa-box mr-2"></i> Detail Produk
                        </p>
                        <div class="flex justify-end mb-4">
                            {{-- DIUBAH: Menggunakan class js-open-modal-btn dan data-target-modal --}}
                            <button type="button" data-target-modal="produkModal"
                                class="js-open-modal-btn bg-brand text-white px-6 py-1 rounded hover:bg-brand-deep transition">
                                + Tambah Produk
                            </button>
                        </div>

                        <div id="cart-list" class="min-h-[200px]"></div> {{-- Tambahkan min-height agar tidak kosong --}}
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="order-1 w-full max-w-full px-3 shrink-0 xl:w-5/12 xl:flex-0 xl:order-2 xl:mt-4 xl:mr-4">
                    {{-- Box 1 Kanan: DATA CUSTOMER --}}
                    <div
                        class="p-3 mb-4 bg-white border border-gray-200 shadow-md rounded-xl dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <p
                                class="mb-4 font-bold tracking-wide text-black uppercase text-base dark:text-white dark:opacity-60">
                                <i class="fas fa-user mr-2"></i> Data Customer
                            </p>
                            <a href="{{ route('kurir.pesanan.index') }}"
                                class="px-2 mb-4 font-semibold text-white rounded-full cursor-pointer text-base bg-brand hover:bg-brand-deep">
                                <i class="fas fa-caret-left"></i> Back
                            </a>
                        </div>

                        <div class="relative w-full mb-4 group">
                            <label for="search-input"
                                class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Nama
                                Customer</label>
                            <div class="relative">
                                <button id="dropdown-button" type="button"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand focus:border-brand block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-left">
                                    <span id="selected-customer">- Pilih Customer -</span>
                                    <svg class="absolute w-5 h-5 ml-2 -mr-1 -translate-y-1/2 right-3 top-1/2"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="dropdown-menu"
                                    class="absolute z-10 hidden w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-700">
                                    <div class="p-2">
                                        <input type="text" id="search-input"
                                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                            placeholder="Cari customer...">
                                    </div>
                                    <ul id="customer-list" class="overflow-y-auto max-h-60">
                                        @foreach ($customers as $customer)
                                            <li>
                                                <a href="#"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-white active:scale-90"
                                                    data-value="{{ $customer->id }}"
                                                    data-company-name="{{ $customer->company_name }}"
                                                    data-phone="{{ $customer->phone }}"
                                                    data-address="{{ $customer->address }}">
                                                    {{ $customer->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <p id="company-name-display" class="hidden mt-2 text-sm text-gray-600 dark:text-gray-400"></p>

                            <input type="hidden" name="customer_id" id="customer-id-input">
                        </div>

                        <div class="mb-4">
                            <label for="phone"
                                class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">No.
                                HP</label>
                            <input type="text" name="phone" id="phone"
                                class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"
                                disabled>
                        </div>

                        <div class="mb-4">
                            <label for="address"
                                class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Alamat</label>
                            <textarea id="address" name="address"
                                class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"
                                disabled>
                                </textarea>
                        </div>
                    </div>

                    {{-- Box 2 Kanan: METODE PEMBAYARAN & CATATAN --}}
                    <div
                        class="p-3 mt-4 bg-white border border-gray-200 shadow-md rounded-xl dark:bg-gray-800 dark:border-gray-700">
                        <p
                            class="mb-4 font-bold tracking-wide text-black uppercase text-base dark:text-white dark:opacity-60">
                            💳 METODE PEMBAYARAN
                        </p>

                        <div class="mb-4">
                            <label for="payment-method-input"
                                class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Metode
                                Pembayaran</label>
                            <div class="relative inline-block w-full text-left">
                                <button id="payment-method-button" type="button"
                                    class="inline-flex justify-between items-center w-full rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-brand dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-offset-gray-800">
                                    <span id="selected-payment-method">-Pilih metode pembayaran -</span>
                                    <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="payment-method-menu"
                                    class="absolute left-0 hidden w-full mt-1 bg-white shadow-lg rounded-xl ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700 dark:ring-gray-600/50"
                                    role="menu">
                                    <div class="py-2" role="none">
                                        <a href="#"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-600"
                                            data-value="cash" role="menuitem"><i class="fas fa-money-bill-wave"></i> Cash
                                            (Tunai)</a>
                                        <a href="#"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-600"
                                            data-value="tf" role="menuitem"><i class="fas fa-money-check"></i> Transfer
                                            Bank</a>
                                        {{-- <a href="#"
                                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-600"
                                                data-value="qr" role="menuitem"><i class="fas fa-qrcode"></i>QRIS</a> --}}
                                    </div>
                                </div>
                                <input type="hidden" id="payment-method-input" name="payment_method">
                            </div>
                        </div>

                        {{-- <div id="payment-proof-upload" class="hidden mb-4">
                                <label for="payment-proof"
                                    class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">Bukti
                                    Pembayaran</label>
                                <input type="file" name="payment_proof" id="payment-proof" accept="image/*"
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-brand focus:outline-none">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG. Ukuran maksimal: 2MB.</p>
                            </div> --}}

                        <div class="mb-4">
                            <label for="note"
                                class="inline-block mb-2 ml-1 text-xs font-bold text-slate-700 dark:text-white/80">📝
                                Catatan</label>
                            <textarea type="text" name="note" id="note"
                                class="block w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:border-brand focus:outline-none dark:bg-slate-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-500"></textarea>
                        </div>

                        <div class="hidden pt-4 mt-4 border-t border-gray-200 dark:border-gray-600 xl:block">
                            <div class="flex items-center justify-between">
                                <span class="text-base font-medium text-gray-900 dark:text-white">Total Pesanan:</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-white cart-total-display">Rp
                                    0</span>
                            </div>
                        </div>

                        <div class="hidden w-full max-w-full mt-4 shrink-0 md:w-full md:flex-0 xl:block">
                            <div class="flex justify-end">
                                <button type="button" onclick="checkout()"
                                    class="w-full max-w-full bg-brand text-white px-6 py-2 rounded-xl hover:bg-brand-deep transition shadow-md">
                                    Checkout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <div
        class="fixed bottom-0 left-0 z-50 flex items-center justify-between w-full p-4 bg-white border-t border-gray-300 dark:bg-gray-800 xl:hidden">
        <p id="cart-total" class="text-lg font-bold cart-total-display dark:text-white">Total: Rp 0</p>
        <button type="button" onclick="checkout()"
            class="bg-brand text-white px-6 py-2 rounded-xl hover:bg-brand-deep transition shadow-md">
            Checkout
        </button>
    </div>


    @push('flowbite-modals')
        @include('dashboard.kurir.pesanan.success-modal')
        @include('dashboard.kurir.pesanan.produk-modal')
        @include('dashboard.kurir.pesanan.konfirmasi-modal')
    @endpush

    <script>
        // --- Data dan State Global ---
        let produkList = [];
        let cart = [];

        function showToast(message, type = 'success', duration = 5000) {
            const existingToast = document.getElementById('toast-notification-dynamic');
            if (existingToast) {
                existingToast.remove();
            }
            let iconSvg, iconBgColor, iconTextColor;
            if (type === 'success') {
                iconBgColor = 'bg-brand-light dark:bg-brand-deep';
                iconTextColor = 'text-brand dark:text-brand-light';
                iconSvg =
                    `<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" /></svg>`;
            } else if (type === 'info') {
                // Menambahkan kondisi untuk tipe 'info' dengan ikon dan warna biru
                iconBgColor = 'bg-blue-100 dark:bg-blue-800';
                iconTextColor = 'text-blue-500 dark:text-blue-200';
                iconSvg =
                    `<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>`;
            } else { // 'error'
                iconBgColor = 'bg-red-100 dark:bg-red-800';
                iconTextColor = 'text-red-500 dark:text-red-200';
                iconSvg =
                    `<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" /></svg>`;
            }
            const toastElement = document.createElement('div');
            toastElement.id = 'toast-notification-dynamic';
            toastElement.className =
                'fixed top-5 right-5 w-full max-w-xs p-4 text-gray-900 bg-white rounded-lg shadow-lg dark:bg-gray-800 dark:text-gray-300 z-[100] transition-transform duration-300 ease-out';
            toastElement.setAttribute('role', 'alert');
            toastElement.style.transform = 'translateY(-20px) translateX(20px)';
            toastElement.style.opacity = '0';
            toastElement.innerHTML = `
                <div class="flex items-center">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${iconTextColor} ${iconBgColor} rounded-lg">${iconSvg}</div>
                    <div class="text-sm font-normal ms-3">${escapeHtml(message)}</div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" /></svg>
                    </button>
                </div>`;
            document.body.appendChild(toastElement);
            setTimeout(() => {
                toastElement.style.transform = 'translateY(0) translateX(0)';
                toastElement.style.opacity = '1';
            }, 10);
            setTimeout(() => {
                toastElement.style.transform = 'translateY(-20px) translateX(20px)';
                toastElement.style.opacity = '0';
                setTimeout(() => toastElement.remove(), 300);
            }, duration);
        }

        // --- Inisialisasi DOM dan Event Listeners ---
        document.addEventListener('DOMContentLoaded', function() {
            // ... (Dropdown logic tetap sama) ...
            const customerDropdownButton = document.getElementById('dropdown-button');
            const customerDropdownMenu = document.getElementById('dropdown-menu');
            const searchInput = document.getElementById('search-input');
            const selectedCustomerSpan = document.getElementById('selected-customer');
            const hiddenCustomerIdInput = document.getElementById('customer-id-input');
            const companyNameDisplay = document.getElementById('company-name-display');
            const phoneInput = document.getElementById('phone');
            const addressInput = document.getElementById('address');
            const customerList = document.getElementById('customer-list');
            customerDropdownButton.addEventListener('click', function(e) {
                e.stopPropagation();
                customerDropdownMenu.classList.toggle('hidden');
                searchInput.focus();
            });
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                Array.from(customerList.children).forEach(li => {
                    const customerName = li.textContent.toLowerCase();
                    if (customerName.includes(searchTerm)) {
                        li.style.display = 'block';
                    } else {
                        li.style.display = 'none';
                    }
                });
            });
            customerList.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    e.preventDefault();
                    const selectedLink = e.target;
                    const customerId = selectedLink.getAttribute('data-value');
                    const customerName = selectedLink.textContent.trim();
                    const companyName = selectedLink.getAttribute('data-company-name');
                    const phone = selectedLink.getAttribute('data-phone');
                    const address = selectedLink.getAttribute('data-address');

                    document.getElementById('selected-customer').textContent = customerName;
                    document.getElementById('customer-id-input').value = customerId;
                    document.getElementById('phone').value = phone;
                    document.getElementById('address').value = address;
                    document.getElementById('dropdown-menu').classList.add('hidden');

                    if (companyName && companyName !== 'null') {
                        companyNameDisplay.textContent = `Toko: ${companyName}`;
                        companyNameDisplay.classList.remove('hidden');
                    } else {
                        companyNameDisplay.classList.add('hidden');
                    }

                    // Panggil fungsi untuk memuat pesanan terakhir setelah customer dipilih
                    loadLastOrder(customerId);
                }
            });
            const paymentButton = document.getElementById('payment-method-button');
            const paymentMenu = document.getElementById('payment-method-menu');
            const selectedPaymentText = document.getElementById('selected-payment-method');
            const hiddenPaymentInput = document.getElementById('payment-method-input');
            // const paymentProofUploadDiv = document.getElementById('payment-proof-upload');
            paymentButton.addEventListener('click', function(e) {
                e.stopPropagation();
                paymentMenu.classList.toggle('hidden');
            });
            paymentMenu.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    e.preventDefault();
                    const value = e.target.getAttribute('data-value');
                    const text = e.target.textContent;
                    selectedPaymentText.textContent = text;
                    hiddenPaymentInput.value = value;
                    // if (value === 'tf' || value === 'qr') {
                    //     paymentProofUploadDiv.classList.remove('hidden');
                    // } else {
                    //     paymentProofUploadDiv.classList.add('hidden');
                    //     document.getElementById('payment-proof').value = '';
                    // }
                    paymentMenu.classList.add('hidden');
                }
            });
            document.addEventListener('click', function(e) {
                if (!customerDropdownButton.contains(e.target) && !customerDropdownMenu.contains(e
                        .target)) {
                    customerDropdownMenu.classList.add('hidden');
                }
                if (!paymentButton.contains(e.target) && !paymentMenu.contains(e.target)) {
                    paymentMenu.classList.add('hidden');
                }
            });

            // Inisialisasi Produk
            getProduk();
        });

        /**
         * FUNGSI BARU: Mengambil data pesanan terakhir dan mengisi cart
         */
        async function loadLastOrder(customerId) {
            // Beri feedback visual bahwa data sedang dimuat
            showToast('Memuat pesanan terakhir...', 'info', 2000);

            try {
                // Panggil endpoint API yang sudah kita buat
                const response = await fetch(`{{ url('/kurir/customer') }}/${customerId}/last-order`);

                if (!response.ok) {
                    throw new Error('Gagal mengambil data pesanan terakhir.');
                }

                const data = await response.json();

                if (data.items && data.items.length > 0) {
                    // Jika ada item, ganti isi cart dengan data baru
                    cart = data.items;
                    showToast('Pesanan terakhir berhasil dimuat.', 'success');
                } else {
                    // Jika tidak ada pesanan, kosongkan cart dan beri info
                    cart = [];
                    showToast('Customer ini tidak memiliki pesanan sebelumnya.', 'info');
                }

                // Render ulang tampilan cart dan tombol produk
                renderCart();
                tampilkanPilihanProduk();

            } catch (error) {
                console.error('Error saat memuat pesanan terakhir:', error);
                showToast(error.message, 'error');
                // Kosongkan cart jika terjadi error untuk menghindari kebingungan
                cart = [];
                renderCart();
                tampilkanPilihanProduk();
            }
        }

        // FUNGSI: Untuk menutup modal produk. Dipanggil dari tambahKeCart
        function hideProdukModal() {
            const modalElement = document.getElementById('produkModal');
            if (modalElement) {
                // Menggunakan fungsi closeModal dari custom-modal.js
                closeModal(modalElement);
            }
        }

        // FUNGSI DIUBAH: Menggunakan openModal() dari custom-modal.js
        function showConfirmationModal() {
            const customerName = document.getElementById('selected-customer').textContent.trim();
            const customerPhone = document.getElementById('phone').value;
            const customerAddress = document.getElementById('address').value;
            const paymentMethodText = document.getElementById('selected-payment-method').textContent.trim();

            document.getElementById('modal-customer-name').textContent = customerName;
            document.getElementById('modal-customer-phone').textContent = customerPhone || '-';
            document.getElementById('modal-customer-address').textContent = customerAddress || '-';
            document.getElementById('modal-payment-method').textContent = paymentMethodText;

            const productListDiv = document.getElementById('modal-product-list');
            productListDiv.innerHTML = '';
            let total = 0;

            cart.forEach(item => {
                const subtotal = item.qty * item.price;
                total += subtotal;
                const productHtml = `
                <div class="flex items-start justify-between text-sm">
                    <div class="flex-grow">
                        <p class="font-semibold text-gray-800 text-base dark:text-gray-200">${escapeHtml(item.product_name)}</p>
                        ${item.variant_name ? `<p class="text-xs text-gray-500 dark:text-gray-400"> ▸ ${escapeHtml(item.variant_name)}</p>` : ''}
                        <p class="text-xs text-gray-600 dark:text-gray-400">${item.qty} x Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <p class="font-semibold text-gray-800 text-base dark:text-gray-200">Rp ${subtotal.toLocaleString('id-ID')}</p>
                </div>`;
                productListDiv.innerHTML += productHtml;
            });

            document.getElementById('modal-total-amount').textContent = `Rp ${total.toLocaleString('id-ID')}`;

            // DIUBAH: Panggil fungsi openModal kustom
            openModal('konfirmasiModal');
        }

        function checkout() {
            // Validasi data
            const customerId = document.getElementById('customer-id-input').value;
            const paymentMethod = document.getElementById('payment-method-input').value;
            // const paymentProofFile = document.getElementById('payment-proof').files[0];

            if (!customerId) {
                showToast('Silakan pilih customer terlebih dahulu.', 'error');
                return;
            }
            if (cart.length === 0) {
                showToast('Keranjang belanja kosong. Tambahkan produk terlebih dahulu.', 'error');
                return;
            }
            if (!paymentMethod || paymentMethod === "-Pilih metode pembayaran -") {
                showToast('Silakan pilih metode pembayaran.', 'error');
                return;
            }
            // if ((paymentMethod === 'tf' || paymentMethod === 'qr') && !paymentProofFile) {
            //     showToast('Silakan unggah bukti pembayaran.', 'error');
            //     return;
            // }

            // Jika valid, tampilkan modal konfirmasi
            showConfirmationModal();
        }

        // FUNGSI DIUBAH: Menggunakan closeModal() dari custom-modal.js
        async function submitOrder() {
            const customerId = document.getElementById('customer-id-input').value;
            const paymentMethod = document.getElementById('payment-method-input').value;
            const note = document.getElementById('note').value;
            const phone = document.getElementById('phone').value;
            const address = document.getElementById('address').value;
            // const paymentProofFile = document.getElementById('payment-proof').files[0];
            const formData = new FormData();
            formData.append('customer_id', customerId);
            formData.append('phone', phone);
            formData.append('address', address);
            formData.append('payment_method', paymentMethod);
            formData.append('note', note);
            formData.append('products', JSON.stringify(cart.map(item => ({
                product_id: item.product_id,
                product_name: item.product_name,
                variant_id: item.variant_id,
                variant_name: item.variant_name,
                quantity: item.qty,
                price: item.price,
            }))));

            // if (paymentProofFile) {
            //     formData.append('payment_proof', paymentProofFile);
            // }

            const submitButton = document.getElementById('submit-order-button');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Menyimpan...';

            try {
                const response = await fetch("{{ route('kurir.orders.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });
                const result = await response.json();

                // DIUBAH: Panggil fungsi closeModal kustom
                const modalElement = document.getElementById('konfirmasiModal');
                closeModal(modalElement);

                if (response.ok) {
                    showSuccessModal(result.message, result.invoice_number);
                    // Reset form
                    cart = [];
                    renderCart();
                    document.getElementById('selected-customer').textContent = '- Pilih Customer -';
                    document.getElementById('customer-id-input').value = '';
                    document.getElementById('phone').value = '';
                    document.getElementById('address').value = '';
                    document.getElementById('selected-payment-method').textContent = '-Pilih metode pembayaran -';
                    document.getElementById('payment-method-input').value = '';
                    // document.getElementById('payment-proof').value = '';
                    // document.getElementById('payment-proof-upload').classList.add('hidden');
                    document.getElementById('note').value = '';

                } else {
                    let errorMessage = result.message || 'Terjadi kesalahan.';
                    if (result.errors) {
                        errorMessage += '\n<ul>';
                        for (const key in result.errors) {
                            errorMessage += `<li>${result.errors[key].join(', ')}</li>`;
                        }
                        errorMessage += '</ul>';
                    }
                    showToast('Gagal: ' + errorMessage, 'error', 7000);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat mengirim pesanan. Mohon coba lagi.', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Konfirmasi & Simpan Pesanan';
            }
        }

        async function getProduk() {
            try {
                const res = await fetch("{{ url('kurir/produk/json') }}");
                produkList = await res.json();
                tampilkanPilihanProduk();
            } catch (err) {
                console.error("Gagal ambil produk:", err);
                showToast('Gagal memuat daftar produk.', 'error');
            }
        }

        function tampilkanPilihanProduk() {
            const pilihDiv = document.getElementById('pilihan-produk');
            pilihDiv.innerHTML = '';
            produkList.forEach(p => {
                let imageUrl = p.image || p.foto || null;
                if (p.variants && p.variants.length > 0) {
                    p.variants.forEach(v => {
                        const sudahDipilih = cart.some(c => c.variant_id === v.id);
                        pilihDiv.innerHTML += `
                        <div class="flex flex-row items-center gap-3 p-3 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            ${imageUrl ? `<img src="${imageUrl}" alt="${escapeHtml(p.name)}" class="object-cover w-16 h-16 mr-2 border rounded" />` : ''}
                            <div class="flex-1">
                                <div class="font-semibold">
                                    <span class="font-bold text-gray-800 text-base dark:text-gray-100">${escapeHtml(p.name)}</span>
                                    ${v.name ? `<div class="mb-1 text-xs text-gray-600 dark:text-gray-300"> ▸ ${escapeHtml(v.name)} </div>` : ''}
                                </div>
                                <div class="font-bold text-brand-deep dark:text-brand-light">Rp ${v.price.toLocaleString()}</div>
                            </div>
                            ${sudahDipilih ?
                                `<button type="button" class="px-2 py-1 text-xs text-white bg-red-500 rounded hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-600" onclick="hapusDariCart(${p.id}, ${v.id})"><i class="fas fa-trash"></i></button>` :
                                `<button type="button" class="px-2 py-1 text-xs text-white bg-brand rounded hover:bg-brand-deep" onclick="tambahKeCart(${p.id}, ${v.id})"><i class="fas fa-cart-plus"></i></button>`
                            }
                        </div>`;
                    });
                } else {
                    const sudahDipilih = cart.some(c => c.product_id === p.id && !c.variant_id);
                    pilihDiv.innerHTML += `
                    <div class="flex flex-row items-center gap-3 p-3 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                        ${imageUrl ? `<img src="${imageUrl}" alt="${escapeHtml(p.name)}" class="object-cover w-16 h-16 mr-2 border rounded" />` : ''}
                        <div class="flex-1">
                            <div class="font-semibold">${escapeHtml(p.name)}</div>
                            <div class="font-bold text-brand-deep dark:text-brand-light">Rp ${p.price ? p.price.toLocaleString() : ''}</div>
                        </div>
                        ${sudahDipilih ?
                            `<button type="button" class="px-2 py-1 text-xs text-white bg-red-500 rounded hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-600" onclick="hapusDariCart(${p.id}, null)"><i class="fas fa-trash"></i></button>` :
                            `<button type="button" class="px-2 py-1 text-xs text-white bg-brand rounded hover:bg-brand-deep" onclick="tambahKeCart(${p.id}, null)"><i class="fas fa-cart-plus"></i></button>`
                        }
                    </div>`;
                }
            });
        }

        function tambahKeCart(productId, variantId) {
            let product = produkList.find(p => p.id === productId);
            let cartItem = null;
            let variant = null;
            if (variantId) {
                variant = product.variants.find(v => v.id === variantId);
                cartItem = cart.find(c => c.variant_id === variantId);
            } else {
                cartItem = cart.find(c => c.product_id === productId && !c.variant_id);
            }
            if (cartItem) {
                cartItem.qty++;
                showToast(
                    `Kuantitas ${product.name}${variant ? ' - ' + variant.name : ''} ditambahkan menjadi ${cartItem.qty}.`,
                    'info');
            } else {
                cart.push({
                    product_id: productId,
                    product_name: product.name,
                    variant_id: variantId || null,
                    variant_name: variant ? variant.name : null,
                    price: variant ? variant.price : (product.price || product.harga),
                    qty: 1
                });
                showToast(`${product.name}${variant ? ' - ' + variant.name : ''} ditambahkan ke keranjang.`, 'success');
            }
            renderCart();
            tampilkanPilihanProduk();
            // Jangan tutup modal, biarkan user menutup manual
        }

        function hapusDariCart(productId, variantId) {
            let product = produkList.find(p => p.id === productId);
            let variant = null;
            let cartItemIndex = -1;
            if (variantId) {
                variant = product.variants.find(v => v.id === variantId);
                cartItemIndex = cart.findIndex(c => c.variant_id === variantId);
            } else {
                cartItemIndex = cart.findIndex(c => c.product_id === productId && !c.variant_id);
            }
            if (cartItemIndex !== -1) {
                cart.splice(cartItemIndex, 1);
                showToast(`${product.name}${variant ? ' - ' + variant.name : ''} dihapus dari keranjang.`, 'info');
                renderCart();
                tampilkanPilihanProduk();
            }
        }

        function ubahQtyCart(idx, change) {
            cart[idx].qty += change;
            if (cart[idx].qty < 1) cart[idx].qty = 1;
            renderCart();
        }

        function setQtyCart(idx, newQty) {
            // Validasi input: pastikan angka dan minimal 1
            let qty = parseInt(newQty, 10);
            if (isNaN(qty) || qty < 1) {
                qty = 1;
            }
            cart[idx].qty = qty;

            // Panggil renderCart() agar total dan subtotal ikut ter-update
            renderCart();
        }

        function hapusProdukCart(idx) {
            const itemToRemove = cart[idx];
            cart.splice(idx, 1);
            renderCart();
            tampilkanPilihanProduk();
            showToast(
                `${itemToRemove.product_name}${itemToRemove.variant_name ? ' - ' + itemToRemove.variant_name : ''} dihapus dari keranjang.`,
                'info');
        }

        function renderCart() {
            const cartDiv = document.getElementById('cart-list');
            cartDiv.innerHTML = '';
            let total = 0;
            if (cart.length === 0) {
                cartDiv.innerHTML =
                    `<div class="p-8 text-center text-gray-500 dark:text-gray-400">Keranjang masih kosong. Tambahkan produk!</div>`;
            } else {
                // Desktop Table
                const desktopTableContainer = document.createElement('div');
                desktopTableContainer.className = 'overflow-x-auto hidden md:block';

                // --- PERUBAHAN DIMULAI DI SINI ---

                let tableHTML =
                    `<table class="min-w-full text-sm text-left text-gray-500 table-fixed dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="w-1/3 px-4 py-3">Produk</th>
                                <th scope="col" class="w-1/6 px-4 py-3">Varian</th>
                                <th scope="col" class="w-1/6 px-2 py-3">Harga</th>
                                <th scope="col" class="w-1/12 px-2 py-3">Qty</th>
                                <th scope="col" class="w-1/6 px-2 py-3">Subtotal</th>
                                <th scope="col" class="w-1/12 px-2 py-3">Aksi</th>
                            </tr>
                        </thead>
                    <tbody>`;
                let mobileCardsHTML = `<div class="md:hidden">`;

                cart.forEach((item, idx) => {
                    const subtotal = item.qty * item.price;
                    total += subtotal;
                    const productData = produkList.find(p => p.id === item.product_id);
                    const imageUrl = productData ? (productData.image || productData.foto ||
                            'https://placehold.co/64x64/E2E8F0/64748B?text=No+Img') :
                        'https://placehold.co/64x64/E2E8F0/64748B?text=No+Img';

                    // --- PERUBAHAN 1 (UNTUK DESKTOP TABLE) ---
                    tableHTML +=
                        `<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-4 py-2 font-medium text-gray-900 break-words dark:text-white">
                                <div class="flex items-center gap-2">
                                    <img src="${imageUrl}" alt="${item.product_name}" class="flex-shrink-0 object-cover w-10 h-10 border rounded" />
                                    <span>${item.product_name}</span>
                                </div>
                            </td>
                            <td class="px-2 py-2 break-words">${item.variant_name ? item.variant_name : '-'}</td>
                            <td class="px-2 py-2 whitespace-nowrap">Rp ${item.price.toLocaleString('id-ID')}</td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <button type="button" class="px-2 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white" onclick="ubahQtyCart(${idx}, -1)">-</button>

                                    <input type="number" min="1" value="${item.qty}"
                                           onchange="setQtyCart(${idx}, this.value)"
                                           class="w-12 py-1 text-center text-sm border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                                    <button type="button" class="px-2 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white" onclick="ubahQtyCart(${idx}, 1)">+</button>
                                </div>
                            </td>
                            <td class="px-2 py-2 font-semibold whitespace-nowrap">Rp ${subtotal.toLocaleString('id-ID')}</td>
                            <td class="px-2 py-2 whitespace-nowrap"><button type="button" class="px-2 py-1 text-xs text-white bg-red-500 rounded hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-600" onclick="hapusProdukCart(${idx})">
                                <i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>`;

                    // --- PERUBAHAN 2 (UNTUK MOBILE CARD) ---
                    mobileCardsHTML +=
                        `<div class="flex items-center gap-3 p-3 mb-2 bg-white border rounded shadow-sm dark:bg-gray-800 dark:border-gray-700">
                            <img src="${imageUrl}" alt="${item.product_name}" class="object-cover w-12 h-12 border rounded" />
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 dark:text-white">${item.product_name}</div>
                                ${item.variant_name ? `<div class="text-xs text-gray-500 dark:text-gray-400">${item.variant_name}</div>` : ''}
                                <div class="text-xs text-gray-600 dark:text-gray-400">Harga: Rp ${item.price.toLocaleString('id-ID')}</div>
                                <div class="flex items-center gap-1 mt-1">
                                    <button type="button" class="px-2 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white" onclick="ubahQtyCart(${idx}, -1)">-</button>

                                    <input type="number" min="1" value="${item.qty}"
                                           onchange="setQtyCart(${idx}, this.value)"
                                           class="w-12 py-1 text-center text-sm border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                                    <button type="button" class="px-2 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white" onclick="ubahQtyCart(${idx}, 1)">+</button>
                                </div>
                                <div class="mt-1 font-semibold">Subtotal: Rp ${subtotal.toLocaleString('id-ID')}</div>
                            </div>
                            <button type="button" class="px-2 py-1 text-xs text-white bg-red-500 rounded hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-600" onclick="hapusProdukCart(${idx})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>`;
                });
                tableHTML += `</tbody></table>`;
                mobileCardsHTML += `</div>`;
                desktopTableContainer.innerHTML = tableHTML;
                cartDiv.appendChild(desktopTableContainer);
                cartDiv.innerHTML += mobileCardsHTML;
            }
            const totalDisplayElements = document.querySelectorAll('.cart-total-display');
            totalDisplayElements.forEach(el => {
                if (el.id === 'cart-total') {
                    el.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
                } else {
                    el.textContent = `Rp ${total.toLocaleString('id-ID')}`;
                }
            });
        }

        function showSuccessModal(message, invoiceNumber) {
            document.getElementById('success-message').textContent = message;
            const invoiceEl = document.getElementById('success-invoice');
            if (invoiceNumber) {
                invoiceEl.textContent = 'No. Invoice: ' + invoiceNumber;
                invoiceEl.classList.remove('hidden');
            } else {
                invoiceEl.classList.add('hidden');
            }
            document.getElementById('successModal').classList.remove('hidden');

            // Tambahkan event listener ke tombol tutup modal agar reload halaman setelah modal ditutup
            setTimeout(() => {
                const closeBtns = document.querySelectorAll('#successModal .js-close-modal-btn');
                closeBtns.forEach(btn => {
                    btn.onclick = function() {
                        document.getElementById('successModal').classList.add('hidden');
                        window.location.reload();
                    };
                });
            }, 100);
        }
    </script>
@endsection
