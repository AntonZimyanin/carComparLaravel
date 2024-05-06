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
        
        if ($data->get("car_model_id")) {
            $carModelId = $data->get("car_model_id");
            $carModelName = $data->get("car_model_name");

            $chat->storage()->set('car_model_id', $carModelId);
            $chat->storage()->set('car_model_name', $carModelName);

            $chat->storage()->set('car_price_state', true);
        }


        $mess = "$carModelId
*Выбырите цену*

Если Вы хотите ввести свое значение, введите минимальную и максимальную цену в $.

Пример сообщения: 150 300

Текущее значение: от 0$ до 0$"
        ;


        $kb = $this->priceKb->getKbWithPagination('set_car_model', 'set_car_price', 3);
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
