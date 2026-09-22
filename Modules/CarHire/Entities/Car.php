<?php

namespace Modules\CarHire\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\CategoryManagement\Entities\Category;

class Car extends Model
{
    protected $fillable = [
        'category_id',
        'car_type_id',
        'brand',
        'model',
        'year',
        'fuel_type',
        'transmission_type',
        'transmission',
        'seating_capacity',
        'daily_rate',
        'hourly_rate',
        'description',
        'images',
        'features',
        'status',
        'coordinates',
        'provider_id',
        'registration_number',
        'air_conditioning',
        'service_type',
        'available_hours_start',
        'available_hours_end',
        'preferred_areas',
        'driving_license',
        'vehicle_registration',
        'insurance_documents',
        'mot_certificate',
        'service_category',
        'pricing_type',
    ];

    protected $casts = [
        'images' => 'array',
        'coordinates' => 'json',
        'features' => 'array',
    ];

    public function provider()
    {
        return $this->belongsTo(\Modules\ProviderManagement\Entities\Provider::class);
    }

    public function type()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function bookings()
    {
        return $this->hasMany(CarBooking::class);
    }

    public function documents()
    {
        return $this->hasMany(CarDocument::class);
    }
}

