<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'is_active', 'escalation_contact_name', 'escalation_contact_phone', 'address', 'operating_hours', 'maps_link', 'contact_email', 'contact_phone'];

    protected $casts = [
        'is_active' => 'boolean',
        'operating_hours' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope untuk hanya mengambil cabang yang masih beroperasi.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Zona waktu operasional cabang, dikirimkan satu sumber kebenaran agar
     * tidak lagi di-hardcode di banyak controller.
     */
    protected function timezone(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match (Str::slug($this->name)) {
                    'denpasar' => 'Asia/Makassar', // WITA
                    'surabaya', 'malang' => 'Asia/Jakarta', // WIB
                    default => config('app.timezone', 'UTC'),
                };
            }
        );
    }
}
