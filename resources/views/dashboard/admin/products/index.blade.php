@extends('layouts.argon')
@section('title', 'Manajemen Produk')
@section('page_title', 'Produk')

@section('content')
    <div class="relative p-6 bg-white shadow-xl rounded-2xl dark:bg-slate-800 dark:border dark:border-slate-700">
        {{-- Header: Title dan Tombol Tambah --}}
        <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-box mr-2 text-blue-500"></i>Daftar Produk
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola produk dan varian untuk region {{ $regionName }}</p>
            </div>
            <button type="button" data-target-modal="create-product-modal"
                class="flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl js-open-modal-btn hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:from-blue-600 dark:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 focus:outline-none dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 transition-all">
                <i class="fas fa-plus mr-2"></i>
                Tambah Produk
            </button>
        </div>

        {{-- Daftar Produk Berdasarkan Kategori --}}
        @forelse ($categories as $category)
            @if ($category->products->isNotEmpty())
                <div class="mb-8">
                    <h3 class="pb-3 mb-4 text-xl font-bold text-gray-800 border-b-2 border-blue-500 dark:text-white dark:border-blue-400">
                        <i class="fas fa-folder mr-2 text-blue-500"></i>{{ $category->name }}
                    </h3>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($category->products as $product)
                            <div
                                class="flex flex-col overflow-hidden transition-all duration-300 bg-white border border-gray-200 shadow-lg rounded-2xl hover:shadow-2xl hover:-translate-y-1 dark:bg-slate-700 dark:border-slate-600">
                                <div class="relative group">
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                                        loading="lazy" class="object-cover w-full h-48 transition-transform duration-300 group-hover:scale-105">
                                    @if (!$product->is_active)
                                        <div
                                            class="absolute inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm">
                                            <span
                                                class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-full shadow-lg">NONAKTIF</span>
                                        </div>
                                    @endif
                                    @if ($product->tag)
                                        <div class="absolute top-3 right-3 px-3 py-1 text-xs font-semibold text-white bg-blue-600 rounded-full shadow-md">{{ $product->tag }}</div>
                                    @endif
                                </div>
                                <div class="flex flex-col flex-grow p-5">
                                    <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white line-clamp-2">{{ $product->name }}</h4>
                                    <div x-data="{ open: false }" class="flex-grow">
                                        <p class="mb-3 text-sm text-gray-600 dark:text-gray-300"
                                            :class="open ? '' : 'line-clamp-3'">{{ $product->description }}</p>
                                        @if (strlen($product->description) > 100)
                                            <button @click="open = !open"
                                                class="mb-2 text-xs font-semibold text-blue-600 focus:outline-none hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                <span x-show="!open">Selengkapnya</span><span x-show="open">Tutup</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="mt-4 p-3 bg-gray-50 rounded-xl dark:bg-slate-600">
                                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-tags mr-1 text-green-500"></i>Varian Harga
                                        </h5>
                                        <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-200">
                                            @forelse ($product->variants->where('is_active', true) as $variant)
                                                <li class="flex justify-between items-center">
                                                    <span class="text-gray-600 dark:text-gray-300">{{ $variant->name }}</span>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                                </li>
                                            @empty
                                                <li class="text-gray-400 italic">Belum ada varian.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <div
                                        class="flex items-center justify-end pt-4 mt-4 space-x-2 border-t border-gray-200 dark:border-slate-600">
                                        <button type="button" data-target-modal="edit-product-modal-{{ $product->id }}"
                                            class="js-open-modal-btn flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors shadow-md">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        <button type="button" data-target-modal="delete-product-modal-{{ $product->id }}"
                                            class="js-open-modal-btn flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-md">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <div class="p-12 text-center bg-gray-50 rounded-xl dark:bg-slate-700">
                <i class="fas fa-box-open text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">Belum ada kategori atau produk yang ditambahkan di region Anda.</p>
            </div>
        @endforelse
    </div>
@endsection

@push('flowbite-modals')
    @include('dashboard.admin.products.create', ['categories' => $all_categories])
    @foreach ($categories as $category)
        @foreach ($category->products as $product)
            @include('dashboard.admin.products.edit', [
                'product' => $product,
                'categories' => $all_categories,
            ])
            @include('dashboard.admin.products.delete', ['product' => $product])
        @endforeach
    @endforeach
@endpush
