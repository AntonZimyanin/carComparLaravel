<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarBrandKb;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Contracts\StorageDriver;


class CarBrand
{

    private CarBrandKb $carBrandKb;

    public function __construct(CarBrandKb $carBrandKb)
    {
        $this->carBrandKb = $carBrandKb;
    }

    public function setCarBrand(TelegraphChat $chat, StorageDriver $storage): void
    {
        $car_brand_text = $storage->get("car_brand");

        $storage->set('car_brand_text', $car_brand_text);

        $mess = "$car_brand_text*Выбырите модель машины*";

        $kb = Keyboard::make()
            ->row([
                Button::make('Audi 100')->action('set_car_model')->param('car_model_name', 'Audi 100'),
            ]);

        $chat->message($mess)->keyboard($kb)->send();
    }
}
