<?php

namespace App\Telegram\Enum;

class AvByCarProperty
{
    public string $chatId;
    public string $carBrand;
    public string $carModelId;
    public int $carPriceLow;
    public int $carPriceHigh;

    public function set(
        string $chatId,
        string $car_brand,
        int $carModelId,
        int $carPriceLow,
        int $carPriceHigh,
    ): void
    {
        $this->chatId = $chatId;
        $this->carBrand = $car_brand;
        $this->carModelId = $carModelId;
        $this->carPriceLow = $carPriceLow;
        $this->carPriceHigh = $carPriceHigh;
    }

    public function get(): AvByCarProperty
    {
        return $this;
    }
}
