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
        $car_model_id = '';
        $lastMessId = $chat->storage()->get('message_id');

        if ($data->get("car_model_id")) {
            $car_model_id = $data->get("car_model_id");
            $chat->storage()->set('car_model_id', $car_model_id);

            $chat->storage()->set('car_price_state', false);
        }

        if (!($car_model_id)){ 
            $chat->action('back_to_settings');
        }

        
        $mess = "$car_model_id
*Выбырите цену*

Если Вы хотите ввести свое значение, введите минимальную и максимальную цену в $.

Пример сообщения: 150 300

Текущее значение: от 0$ до 0$"
        ;


        $kb = $this->priceKb->getKbWithPagination('set_car_model', 'set_car_price', 3);
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
