<?php

namespace App\Telegram\KeyboardActions;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\FSM\CarFSM;
use App\Telegram\FSM\StateManager;
use App\Telegram\Traits\ShowPreference;

use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class SetCarPrice
{
    use ShowPreference;
    public const SETUP_COMPLETE = '*Настройка завершена!*';
    public const YOUR_SETTINGS = 'Ваши настройки️:';
    public const PREFERRED_CARS = 'Предпочитаемые машины:';
    public function __construct(
        protected PaginationKb $paginationKb,
        protected CarFSM $carFSM,
        protected CarPreferenceController $carPrefController
    )
    {
    }

    private function appendToMess(mixed $value, string $label, string &$mess): void
    {
        if (!empty($value) ){
            $mess .= "*$label*\n$value\n";
        }
    }
    /**
     * @throws StorageException
     */
    public function handle(TelegraphChat $chat, Collection $data, StateManager $state): void
    {
        $lastMessId = $chat->storage()->get('message_id');
        $twinStep = "\n\n";
        $mess = self::SETUP_COMPLETE . $twinStep . self::YOUR_SETTINGS . $twinStep . self::PREFERRED_CARS . $twinStep;

        $carBrand = $state->getData($this->carFSM->carBrand);
        $carModel = $state->getData($this->carFSM->carModel);

        $this->appendToMess($carBrand, 'Бренд машины:', $mess);
        $this->appendToMess($carModel, 'Модель машины:', $mess);

        $carPriceLow = $state->getData($this->carFSM->carPriceLow);

        if (empty($value)) {
            $state->forgetState($this->carFSM->carPriceLow);
            $carPriceLow = 0;
        }
        $carPriceHigh = $data->get("car_price_high") ?? $state->getData($this->carFSM->carPriceHigh);
        //TODO: check only data store, 'cause you store the $carPriceHigh in the main class
        if ( !empty($carPriceHigh)) {
            $state->setData($this->carFSM->carPriceHigh, $carPriceHigh);
            $mess .= "*Ценовой диапозон:*\n " . $carPriceLow . " - " . $carPriceHigh . "\n";
        }

        $updateId = $chat->storage()->get('update_id');

        if ($updateId) {
            $this->carPrefController->update($chat->id, $updateId, [
                'car_brand' => $carBrand,
                'car_model' => $carModel,
                'car_price_low' => $carPriceLow,
                'car_price_high' => $carPriceHigh
            ]);
            $mess .= "*Настройки изменены!*\nЧтобы найти нужные вам машины кликните на кнопку 🔍 или нажмите /search \nНастроить новый фильтр - /settings";
        }
        else {
            $mess .= "Чтобы найти нужные вам машины кликните на кнопку 🔍 или нажмите /search \nЧтобы *сохранить текущий фильтр* нажмите /store\nНастроить новый фильтр - /settings";
        }

        $kb = $this->paginationKb->addPaginationToKb(Keyboard::make(), "set_car_price", "back_to_settings");

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
