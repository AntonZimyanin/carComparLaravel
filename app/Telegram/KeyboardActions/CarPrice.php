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
    const SETUP_COMPLETE = '*Настройка завершена!*';
    const YOUR_SETTINGS = 'Ваши настройки️:';
    const PREFERRED_CARS = 'Предпочитаемые машины:';

    public function __construct(PaginationKb $paginationKb, AvByParser $parser)
    {
        $this->paginationKb = $paginationKb;
        $this->parser = $parser;
    }

    private function appendToMess(string $key, string $label, string &$mess, TelegraphChat $chat): void
    {
        $value = $chat->storage()->get($key);
        if ($value !== null) {
            $mess .= "*$label*\n$value\n";
        }
    }

    /**
     * @throws StorageException
     */
    public function setCarPrice(TelegraphChat $chat, Collection $data): void
    {
        $mess = self::SETUP_COMPLETE."\n"."\n".self::YOUR_SETTINGS."\n"."\n".self::PREFERRED_CARS."\n"."\n";
        $car_price_low = 0;
        $car_price_high = 0;

        $lastMessId = $chat->storage()->get('message_id');
        $this->appendToMess('car_brand_text', 'Бренд машины:', $mess, $chat);
        $this->appendToMess('car_model_id', 'Модель машины:', $mess, $chat);

        if ($data->get("car_price")) {
            $car_price_high = $data->get("car_price");
            $chat->storage()->set('car_price_high', $car_price_high);
            $mess .= "*Ценовой диапозон:*\n ".$car_price_low." - ".$car_price_high."\n";
        }

        $kb = $this->paginationKb->addPaginationToKb(Keyboard::make(), "set_car_price");
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();

        $this->parser->set(
            $chat->storage()->get('car_brand_text'),
            $chat->storage()->get('car_model_id'),
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

