<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppBusinessNumber extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_business_numbers';

    protected $fillable = [
        'phone_number',
        'region_id',
        'provider',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Scope untuk hanya nomor aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
