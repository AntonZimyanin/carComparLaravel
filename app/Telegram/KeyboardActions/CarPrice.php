<?php

namespace App\Telegram\KeyboardActions;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Contracts\StorageDriver;
use Illuminate\Support\Collection;


//TODO : save data to DB or Redis cache
class CarPrice
{

    // private CarModelKb $carModel;

    public function __construct()
    {
    }

    /**
     * @throws StorageException
     */
    public function setCarPrice(TelegraphChat $chat, Collection|null $data): void
    {
        if ($data == null) {
            return;
        }
        $car_model = $chat->storage()->get('car_model_name');
        $car_brand = $chat->storage()->get('car_brand_text');

        $car_price = $data->get("car_price");
        $mess = "   *Настройка завершена!*

        Ваши настройки️:
        Предпочитаемые машины:
        $car_price
        $car_model
        $car_brand

        ";

        $chat->message($mess)->send();

        // $chat->storage()->forget('car_model_name');
        // $chat->storage()->forget('car_price');
        // $chat->storage()->forget('car_brand_text');
        // $chat->storage()->forget('car_model_text');
    }
}
