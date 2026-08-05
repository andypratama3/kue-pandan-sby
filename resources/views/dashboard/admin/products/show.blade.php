@extends('layouts.argon')
@section('title', 'Detail Produk')
@section('page_title', 'Detail Produk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">{{ $product->name }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    {{-- Perbaikan: Gunakan Storage::url() dan path yang konsisten --}}
                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                        class="rounded shadow img-fluid">
                </div>
                <div class="col-md-8">
                    <h3>Deskripsi</h3>
                    <p>{{ $product->description }}</p>
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="font-semibold">Harga</h4>
                            <p class="text-2xl font-bold text-success">Rp{{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="flex justify-start">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
