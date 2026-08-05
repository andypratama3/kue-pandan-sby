@extends('layouts.argon')
@section('title', 'Manajemen Produk')
@section('page_title', 'Katalog Produk')

@section('content')
    <div class="space-y-6">
        <!-- Main Container Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-6">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-9 h-9 rounded-2xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-box text-base"></i>
                        </div>
                        <span>Katalog Produk Kue</span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Kelola varian rasa, harga, dan ketersediaan produk di Cabang {{ $regionName }}.
                    </p>
                </div>

                <button type="button" data-target-modal="create-product-modal"
                    class="js-open-modal-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-brand to-brand-deep hover:from-brand-deep hover:to-brand-deep rounded-2xl shadow-lg shadow-brand-deep/25 transition-all hover:scale-105 active:scale-95">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Produk Baru</span>
                </button>
            </div>

            <!-- Categories & Product Cards Grid -->
            @forelse ($categories as $category)
                @if ($category->products->isNotEmpty())
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-brand/20">
                            <i class="fas fa-layer-group text-brand text-sm"></i>
                            <h3 class="text-base font-extrabold text-slate-800 dark:text-white">
                                {{ $category->name }}
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-light text-brand-deep dark:bg-brand-deep dark:text-brand-light">
                                {{ $category->products->count() }} produk
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            @foreach ($category->products as $product)
                                <div class="flex flex-col overflow-hidden bg-white dark:bg-slate-800/80 border border-slate-200/70 dark:border-slate-700 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                    
                                    <!-- Image Preview Header -->
                                    <div class="relative group h-48 overflow-hidden bg-slate-100 dark:bg-slate-700/50">
                                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                                            loading="lazy" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110">
                                        
                                        @if (!$product->is_active)
                                            <div class="absolute inset-0 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm">
                                                <span class="px-3 py-1 text-xs font-black tracking-wider text-white bg-rose-600 rounded-full shadow-md uppercase">
                                                    Nonaktif
                                                </span>
                                            </div>
                                        @endif

                                        @if ($product->tag)
                                            <div class="absolute top-3 right-3 px-2.5 py-1 text-[10px] font-extrabold text-white bg-brand-deep/90 backdrop-blur-md rounded-full shadow-md">
                                                {{ $product->tag }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Info & Variants -->
                                    <div class="flex flex-col flex-grow p-5 space-y-3">
                                        <h4 class="text-base font-extrabold text-slate-800 dark:text-white line-clamp-1">
                                            {{ $product->name }}
                                        </h4>

                                        <div x-data="{ open: false }" class="flex-grow">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed"
                                                :class="open ? '' : 'line-clamp-2'">{{ $product->description }}</p>
                                            @if (strlen($product->description) > 90)
                                                <button @click="open = !open"
                                                    class="mt-1 text-[11px] font-bold text-brand hover:text-brand-deep dark:text-brand-light transition-colors">
                                                    <span x-show="!open">Baca selengkapnya</span>
                                                    <span x-show="open">Sembunyikan</span>
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Variant Prices Box -->
                                        <div class="p-3 bg-slate-50 dark:bg-slate-700/50 rounded-2xl space-y-2 border border-slate-100 dark:border-slate-700">
                                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-tags text-brand"></i> Varian Harga
                                                </span>
                                            </div>
                                            <ul class="space-y-1 text-xs">
                                                @forelse ($product->variants->where('is_active', true) as $variant)
                                                    <li class="flex justify-between items-center text-slate-700 dark:text-slate-200 font-medium">
                                                        <span>{{ $variant->name }}</span>
                                                        <span class="font-extrabold text-brand-deep dark:text-brand-light">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                                    </li>
                                                @empty
                                                    <li class="text-slate-400 italic text-[11px]">Belum ada varian aktif.</li>
                                                @endforelse
                                            </ul>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700/80">
                                            <button type="button" data-target-modal="edit-product-modal-{{ $product->id }}"
                                                class="js-open-modal-btn inline-flex items-center px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300 rounded-xl transition-colors">
                                                <i class="fas fa-edit mr-1.5"></i>Edit
                                            </button>
                                            <button type="button" data-target-modal="delete-product-modal-{{ $product->id }}"
                                                class="js-open-modal-btn inline-flex items-center px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 rounded-xl transition-colors">
                                                <i class="fas fa-trash-alt mr-1.5"></i>Hapus
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <div class="p-12 text-center bg-slate-50 dark:bg-slate-800/40 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400">
                        <i class="fas fa-box-open text-2xl"></i>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                        Belum ada kategori atau produk yang ditambahkan di Cabang {{ $regionName }}.
                    </p>
                </div>
            @endforelse

        </div>
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
