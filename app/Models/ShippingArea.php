<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'area_name',
        'distance_km',
        'shipping_fee',
        'notes',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'shipping_fee' => 'integer',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
