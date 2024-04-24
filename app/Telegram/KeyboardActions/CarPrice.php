<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Enum\AvByCarProperty;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;


//TODO : save data to DB or Redis cache
class CarPrice
{
    private PaginationKb $paginationKb;

    public function __construct(PaginationKb $paginationKb)
    {
        $this->paginationKb = $paginationKb;
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
        $car_price_low = 0;
        $car_price_high = $data->get("car_price");

        $lastMessId = $chat->storage()->get('message_id');


        $mess = "*Настройка завершена!*

        Ваши настройки️:
        Предпочитаемые машины:
        *Бренд машины:*
        ".$car_brand."
        \n
        *Модель машины:*
        ".$car_model."
        \n
        *Ценовой диапозон:*
        ".$car_price_low."
        ".$car_price_high."
        \n";
        

        $kb = $this->paginationKb->addPaginationToKb(Keyboard::make(), "set_car_price");
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
        
        // write data to db
        // $carProperty = new AvByCarProperty(
        //     $car_brand,
        //     $car_model,
        //     0,
        //     $car_price
        // );

    }
}
