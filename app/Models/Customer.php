<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'company_name',
        'address',
        'landmark',
        'phone',
        'opening_hours',
        'payment_type',
        'note',
        'region_id',
        'customer_category_id',
        'added_by_user_id',
    ];

    /**
     * Mendefinisikan relasi ke model Region.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Mendefinisikan relasi ke model CustomerCategory.
     */
    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

    /**
     * Mendefinisikan relasi ke model User (untuk mengetahui siapa yang menambahkan).
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    /**
     * Mendefinisikan relasi ke model Order.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Mutator untuk format otomatis 'name'.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    /**
     * Mutator untuk format otomatis 'address'.
     */
    protected function address(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    /**
     * Mutator untuk format otomatis 'company_name'.
     */
    protected function companyName(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? ucwords(strtolower($value)) : null
        );
    }
}
