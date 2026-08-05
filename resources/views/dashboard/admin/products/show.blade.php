@extends('layouts.argon')
@section('title', 'Detail Produk')
@section('page_title', 'Detail Produk')

@section('content')
    <div class="space-y-6">
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm space-y-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="w-9 h-9 rounded-2xl bg-brand/10 text-brand-deep dark:text-brand-light flex items-center justify-center">
                            <i class="fas fa-box text-base"></i>
                        </div>
                        <span>{{ $product->name }}</span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Detail varian harga dan deskripsi produk.
                    </p>
                </div>
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-brand-deep dark:text-brand-light bg-mint hover:bg-brand-light dark:bg-brand-deep/50 rounded-xl transition-colors">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    Kembali ke Katalog
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800">
                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                            class="object-cover w-full h-64 md:h-full" />
                    </div>
                    @if (!$product->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 mt-3 text-[10px] font-extrabold tracking-wider text-white bg-rose-600 rounded-full shadow-md uppercase">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div class="md:col-span-2 space-y-5">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fas fa-align-left text-brand text-sm"></i>
                            Deskripsi
                        </h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ $product->description }}</p>
                    </div>

                    @if ($product->tag)
                        <div>
                            <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold text-white bg-brand-deep/90 rounded-full shadow-md">
                                <i class="fas fa-tag mr-1"></i>{{ $product->tag }}
                            </span>
                        </div>
                    @endif

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700 space-y-3">
                        <h3 class="text-base font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fas fa-tags text-brand text-sm"></i>
                            Varian Harga
                        </h3>
                        <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($product->variants->where('is_active', true) as $variant)
                                <li class="flex items-center justify-between py-2.5 text-xs">
                                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ $variant->name }}</span>
                                    <span class="font-extrabold text-brand-deep dark:text-brand-light tabular-nums">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                </li>
                            @empty
                                <li class="py-2.5 text-xs italic text-slate-400">Belum ada varian aktif.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
