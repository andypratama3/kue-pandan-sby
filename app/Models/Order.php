<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'phone',
        'address',
        'total_amount',
        'payment_method',
        'payment_proof',
        'note',
        'rejection_note',
        'created_by_user_id',
        'region_id',
        'status',
        'source',
        'paid_at',
        'picked_up_at',
        'delivered_at',
        'received_by_buyer_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'received_by_buyer_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the items for the order.
     * INI ADALAH RELASI YANG BENAR UNTUK MENGAMBIL PRODUK
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke User (pembuat pesanan)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // Relasi ke Region
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get all of the returns for the Order.
     */
    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function orderReturn()
    {
        return $this->hasOne(OrderReturn::class);
    }
}
