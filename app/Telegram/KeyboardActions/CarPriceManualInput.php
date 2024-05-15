<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\FSM\CarFSM;
use App\Telegram\FSM\StateManager;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class CarPriceManualInput
{
    private SetCarPrice $setCarPrice;
    private CarFSM $carFSM;
    public function __construct(SetCarPrice $setCarPrice, CarFSM $carFSM)
    {
        $this->setCarPrice = $setCarPrice;
        $this->carFSM = $carFSM;
    }

    /**
     * @throws StorageException
     */
    public function handle(TelegraphChat $chat, Collection $data, StateManager $state, string $messageText): void
    {
        $res = explode(' ', $messageText);

        if (count($res) == 2 && is_numeric($res[0]) && is_numeric($res[1])) {
            $state->setData($this->carFSM->carPriceLow, $res[0]);
            $state->setData($this->carFSM->carPriceHigh, $res[1]);

            $this->setCarPrice->handle($chat, $data, $state);

            $chat->message("Цена успешно установлена: $res[0] - $res[1]")->send();
            $state->forgetState($this->carFSM->carPriceLow);
            return;
        }
        $chat->message("Введите ценовой диапазон в формате *от до*\nПример: 100 200")->send();
    }
}
