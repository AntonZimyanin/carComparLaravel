<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class ParsedCarsKb
{
    public function get(int $carId, int $carCount, string $carBrand) : Keyboard {

        $pageNumber = $carId + 1;
        $prevPage = max(0, $carId - 1);
        $nextPage = min($carCount - 1, $carId + 1);

        return Keyboard::make()->row([
            Button::make("{$pageNumber}/$carCount")->action('page_number')->param('page_number', $pageNumber),
        ])
            ->row([
                Button::make('Назад')->action('show_parsed_cars')->param('car_id', $prevPage)->param('brand', $carBrand),
                Button::make('Вперед')->action('show_parsed_cars')->param('car_id', $nextPage)->param('brand', $carBrand),
            ]);
    }
}
