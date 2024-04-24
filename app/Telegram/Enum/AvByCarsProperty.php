<?php

namespace App\Telegram\Enum;

class AvByCarProperty { 

    public string $car_brand;
    public string $car_model; 
    public int $car_price_low;
    public int $car_price_high;


    public function __construct(
        string $car_brand,
        string $car_model,
        int $car_price_low,
        int $car_price_high
    ) {
       
        $this->car_brand = $car_brand;
        $this->car_model = $car_model;
        $this->car_price_low = $car_price_low;
        $this->car_price_high = $car_price_high;
    }
}