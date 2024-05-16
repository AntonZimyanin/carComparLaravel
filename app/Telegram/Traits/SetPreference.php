<?php

namespace App\Telegram\Traits;

trait SetPreference
{
    public function setPreference(array $carProperty, int $chatId, &$property): void
    {
        $property->set(
            $chatId,
            empty($carProperty['carBrand']) ? '' : $carProperty['carBrand'],
            empty($carProperty['carModel']) ? '' : $carProperty['carModel'],
            empty($carProperty['carPriceLow']) ? 0 : (int)$carProperty['carPriceLow'],
            empty($carProperty['carPriceLow']) ? 0 : (int)$carProperty['carPriceHigh'],
        );
    }
}
