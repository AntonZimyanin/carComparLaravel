<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarModelKb;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Contracts\StorageDriver;



class CarModel
{

    private CarModelKb $carModel;

    public function __construct(CarModelKb $carModelKb)
    {
        $this->carModel = $carModelKb;
    }


    public function setCarModel(TelegraphChat $chat, StorageDriver $storage): void
    {
        $car_model_name = $storage->get("car_model_name");
        $storage->set('car_model_name', $car_model_name);

        $mess = "$car_model_name*Выбырите цену*";

        $kb = Keyboard::make()
            ->row([
                Button::make('100$')->action('set_car_price')->param('car_price', '100$'),
            ]);
        $chat->message($mess)->keyboard($kb)->send();
    }
}
