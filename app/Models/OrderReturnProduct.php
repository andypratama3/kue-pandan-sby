<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_return_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',    // Tambahkan
        'subtotal', // Tambahkan
    ];

    /**
     * Relasi ke pengajuan retur induk.
     */
    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class);
    }

    /**
     * Relasi ke produk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke varian produk (jika ada).
     * Nama relasi harus 'variant' agar bisa diakses sebagai $p->variant di controller.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
