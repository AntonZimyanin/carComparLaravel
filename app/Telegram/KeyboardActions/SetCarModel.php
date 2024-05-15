<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\FSM\CarFSM;
use App\Telegram\FSM\StateManager;
use App\Telegram\Keyboards\Builder\Trait\KbWithPagination;

use App\Telegram\Keyboards\PriceKb;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

use Illuminate\Support\Collection;

class SetCarModel
{
    use KbWithPagination;
    private PriceKb $priceKb;
    private CarFSM $carFSM;

    public function __construct(PriceKb $priceKb, CarFSM $carFSM)
    {
        $this->priceKb = $priceKb;
        $this->carFSM = $carFSM;
    }

    /**
     * @throws StorageException
     */
    public function handle(TelegraphChat $chat, Collection|null $data, StateManager $state): void
    {
        $lastMessId = $chat->storage()->get('message_id');
        $carModelName = $data->get('car_model_name');
        if ($carModelName) {
            $carModelName = $data->get("car_model_name");
            $state->setData($this->carFSM->carModel, $carModelName);
//            $state->setState($this->carFSM->carPriceLow);
        }

        $mess = "
*Выбырите цену*

Если Вы хотите ввести свое значение, введите минимальную и максимальную цену в $.

Пример сообщения: 150 300

Текущее значение: от 0$ до ∞$"
        ;

        $kb = $this->priceKb->getKbWithPagination('set_car_model', 'set_car_price', 3);
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
