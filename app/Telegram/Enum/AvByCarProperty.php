<?php

namespace App\Telegram\Enum;

class AvByCarProperty
{
    public string $car_brand;
    public string $car_model_id;
    public int $car_price_low;
    public int $car_price_high;

    public function set(
        string $car_brand,
        int $car_model_id,
        int $car_price_low,
        int $car_price_high
    ) {
        $this->car_brand = $car_brand;
        $this->car_model_id = $car_model_id;
        $this->car_price_low = $car_price_low;
        $this->car_price_high = $car_price_high;
    }

    public function get(): AvByCarProperty
    {
        return $this;
    }
}
