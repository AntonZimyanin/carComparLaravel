<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\PriceKb;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

use Illuminate\Support\Collection;

class CarModel
{
    private PriceKb $priceKb;

    public function __construct(PriceKb $priceKb)
    {
        $this->priceKb = $priceKb;
    }


    /**
     * @throws StorageException
     */
    public function setCarModel(TelegraphChat $chat, Collection|null $data): void
    {

        $lastMessId = $chat->storage()->get('message_id');

        if ($data->get("car_model_id") && $data->get("car_model_id") !== '') {
            $car_model_id = $data->get("car_model_id");
            $chat->storage()->set('car_model_id', $car_model_id);
        }
        $mess = "$car_model_id*Выбырите цену*";

        $kb = $this->priceKb->getKbWithPagination('set_car_model');
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
