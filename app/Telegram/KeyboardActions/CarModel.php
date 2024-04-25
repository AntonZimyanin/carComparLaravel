<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Keyboards\PriceKb;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

use Illuminate\Support\Collection;


class CarModel
{
    private PaginationKb $paginationKb;
    private PriceKb $priceKb;

    public function __construct(PriceKb $priceKb, PaginationKb $paginationKb)
    {
        $this->priceKb = $priceKb;
        $this->paginationKb = $paginationKb;
    }


    /**
     * @throws StorageException
     */
    public function setCarModel(TelegraphChat $chat, Collection|null $data): void
    {
        // if ($data['car_model_name'] !== null) {
        //     $car_model_name = $data->get("car_model_name");

        //     $chat->storage()->set('car_model_name', $car_model_name);
        // }

//        $car_model_name = $data->get("car_model_name");
        $car_model_id = $data->get("car_model_id");

//        $chat->storage()->set('car_model_name', $car_model_name);
        $chat->storage()->set('car_model_id', $car_model_id);

        $lastMessId = $chat->storage()->get('message_id');

        $mess = "$car_model_id*Выбырите цену*";

        $lastMessId = $chat->storage()->get('message_id');

        $kb = $this->priceKb->getInlineKb();
        $kb = $this->paginationKb->addPaginationToKb($kb, 'set_car_model');

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
