<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_brand',
        'car_model',
        'car_price_low',
        'car_price_high',
    ];
}
