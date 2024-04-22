<?php

namespace App\Telegram\KeyboardActions;

use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Contracts\StorageDriver;


class CarPrice
{

    // private CarModelKb $carModel;

    public function __construct()
    {
    }

    public function setCarPrice(TelegraphChat $chat, StorageDriver $storage): void
    {
        $car_price = $storage->get("car_price");
        $car_model = $storage->get('car_model_name');
        $car_brand = $storage->get('car_brand_text');
        $mess = "   *Настройка завершена!*

        Ваши настройки️:
        Предпочитаемые машины:
        $car_price
        $car_model
        $car_brand 

        ";

        $chat->message($mess)->send();
    }
}
