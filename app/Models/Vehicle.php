<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'fuel_type', 'transmission', 'price_per_day',
        'seats', 'model_year', 'description', 'features', 'image',
        'gallery', 'status',
    ];

    protected $casts = [
        'features' => 'array',
        'gallery' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
