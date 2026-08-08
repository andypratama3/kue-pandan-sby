<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'region_id', // Tambahkan region_id
        'name',
        'description',
        'image_path',
        'tag',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Tambahkan relasi ke Region
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
