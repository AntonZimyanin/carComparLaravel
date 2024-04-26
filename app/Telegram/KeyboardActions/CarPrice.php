<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Enum\AvByCarProperty;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

use App\Telegram\Parser\AvBy\AvByParser;

//TODO : save data to DB or Redis cache
class CarPrice
{
    private PaginationKb $paginationKb;
    private AvByParser $parser;

    public function __construct(PaginationKb $paginationKb, AvByParser $parser)
    {
        $this->paginationKb = $paginationKb;
        $this->parser = $parser;
    }

    /**
     * @throws StorageException
     */
    public function setCarPrice(TelegraphChat $chat, Collection $data): void
    {
        if ($data->get("car_price")) {
            return;
        }
        $car_model_id = $chat->storage()->get('car_model_id');
        $car_brand = $chat->storage()->get('car_brand_text');
        $car_price_low = 0;
        $car_price_high = $data->get("car_price");
        $chat->storage()->set('car_price_high', $car_price_high);

        $lastMessId = $chat->storage()->get('message_id');


        $mess = "*Настройка завершена!*

        Ваши настройки️:
        Предпочитаемые машины:
        *Бренд машины:*
        ".$car_brand."
        \n
        *Модель машины:*
        ".$car_model_id."
        \n
        *Ценовой диапозон:*
        ".$car_price_low."
        ".$car_price_high."
        \n";


        $kb = $this->paginationKb->addPaginationToKb(Keyboard::make(), "set_car_price");
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();

        $this->parser->set(
            $car_brand,
            $car_model_id,
            $car_price_low,
            $car_price_high,
        );
        $chat->message($this->parser->url_r())->send();
        $this->parser->parse($chat);


        // write data to db
        // $carProperty = new AvByCarProperty(
        //     $car_brand,
        //     $car_model,
        //     0,
        //     $car_price
        // );

    }
}
