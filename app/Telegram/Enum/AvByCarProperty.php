<?php

namespace App\Telegram\Enum;

class AvByCarProperty
{
    public int $chatId;
    public string $carBrand;
    public string $carModelName;
    public int $carPriceLow;
    public int $carPriceHigh;

    public function set(
        int $chatId,
        string $carBrand = '',
        string $carModelName = '',
        int $carPriceLow = 0,
        int $carPriceHigh = 0,
    ): void {
        $this->chatId = $chatId;
        $this->carBrand = $carBrand;
        $this->carModelName = $carModelName;
        $this->carPriceLow = $carPriceLow;
        $this->carPriceHigh = $carPriceHigh;
    }

    public function get(): AvByCarProperty
    {
        return $this;
    }
}
