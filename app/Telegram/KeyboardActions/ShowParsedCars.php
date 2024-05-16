<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\KeyboardActions\Contracts\BaseAction;
use App\Telegram\Keyboards\ParsedCarsKb;
use App\Telegram\Message\CarPrefMessage;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;

class ShowParsedCars
{
    public function __construct(
        protected ParsedCarsKb $parsedCarsKb,
        protected CarPrefMessage $carPrefMessage
    )
    {
    }

    public function handle(TelegraphChat $chat, Collection $data): void
    {
        $lastMessId = $chat->storage()->get('car_list_message_id');
        $carId = (int)$data->get('car_id');
        $carBrand = $data->get('brand');

        $car =  Redis::hGetAll("car:{$carBrand}:$carId");
        $carCount = (int)Redis::get('car_count');
        $kb = $this->parsedCarsKb->get(
            $carId,
            $carCount,
            $carBrand
        );

        $chat->edit($lastMessId)->message(
            $this->carPrefMessage->get($car)
        )->keyboard($kb)->send();
    }

}
