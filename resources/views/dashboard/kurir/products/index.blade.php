@extends('layouts.argon')
@section('title', 'Katalog Produk')
@section('page_title', 'Katalog Produk')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-deep via-brand-deep to-brand-deep p-6 sm:p-8 text-white shadow-xl shadow-brand-deep/10">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold tracking-wide text-brand-light border border-white/20">
                        <i class="fas fa-store"></i>
                        <span>Cabang {{ $regionName }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Katalog Produk & Harga</h1>
                    <p class="text-xs sm:text-sm text-brand-light/90 max-w-xl">
                        Referensi harga resmi untuk melayani pemesanan. Harga dihitung otomatis oleh sistem saat checkout.
                    </p>
                </div>
            </div>
        </div>

        {{-- Daftar Kategori --}}
        @forelse ($categories as $category)
            <div class="rounded-3xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 p-5 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-tag text-brand"></i>
                        <span>{{ $category->name }}</span>
                        <span class="text-[11px] font-semibold text-slate-400">({{ $category->products->count() }} produk)</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($category->products as $product)
                        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden bg-slate-50/70 dark:bg-slate-800/40 hover:shadow-lg transition-all">
                            <div class="relative h-36 bg-brand-light/30 dark:bg-slate-800">
                                @if ($product->image_path)
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                                        class="object-cover w-full h-full">
                                @else
                                    <div class="flex items-center justify-center w-full h-full">
                                        <i class="text-4xl fas fa-cake-candles text-brand-deep/30"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-sm font-extrabold text-slate-800 dark:text-white mb-2">{{ $product->name }}</h3>
                                <div class="space-y-1.5">
                                    @foreach ($product->variants as $variant)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-600 dark:text-slate-300 font-medium">{{ $variant->name }}</span>
                                            <span class="font-bold text-brand-deep dark:text-brand tabular-nums">
                                                Rp {{ number_format($variant->price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 p-10 text-center shadow-sm">
                <i class="fas fa-box-open text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Belum ada produk aktif di cabang {{ $regionName }}.</p>
            </div>
        @endforelse
    </div>
@endsection
