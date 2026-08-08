<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_id',
        'region_id',
        'status',
        'reason',
        'total_amount_returned',
        'return_proof',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount_returned' => 'decimal:2',
    ];

    public function returnedProducts()
    {
        return $this->hasMany(OrderReturnProduct::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi baru ke kurir dan region
    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
