<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'service_type_id',
        'booking_code',
        'booking_date',
        'start_time',
        'end_time',
        'base_price',
        'weekend_surcharge',
        'total_price',
        'payment_status',
        'payment_url',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'base_price' => 'decimal:2',
        'weekend_surcharge' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    // public function isWeekend()
    // {
    //     $weekDay = $this->booking_date->dayOfWeek;
    //     return $weekDay == 0 || $weekDay == 6; // 0 = Sunday, 6 = Saturday
    // }

    // public function calculateTotalPrice()
    // {
    //     $basePrice = $this->serviceType->price;
    //     $weekendSurcharge = $this->isWeekend() ? 50000 : 0;

    //     $this->base_price = $basePrice;
    //     $this->weekend_surcharge = $weekendSurcharge;
    //     $this->total_price = $basePrice + $weekendSurcharge;

    //     return $this->total_price;
    // }
}
