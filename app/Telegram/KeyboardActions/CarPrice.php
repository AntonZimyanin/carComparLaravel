<?php

namespace App\Telegram\KeyboardActions;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Enum\AvByCarProperty;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;


//TODO : save data to DB or Redis cache
class CarPrice
{
    private AvByCarProperty $property;
    private CarPreferenceController $carPrefController;
    private PaginationKb $paginationKb;
    const SETUP_COMPLETE = '*Настройка завершена!*';
    const YOUR_SETTINGS = 'Ваши настройки️:';
    const PREFERRED_CARS = 'Предпочитаемые машины:';

    public function __construct(PaginationKb $paginationKb, CarPreferenceController $carPrefController, AvByCarProperty $property)
    {
        $this->carPrefController = $carPrefController;
        $this->paginationKb = $paginationKb;
        $this->property = $property;
    }

    private function appendToMess(string $key, string $label, string &$mess, TelegraphChat $chat): string|int
    {
        $value = $chat->storage()->get($key);
        if ($value !== null) {
            $mess .= "*$label*\n$value\n";
        }
        return $value;
    }

    /**
     * @throws StorageException
     */
    public function setCarPrice(TelegraphChat $chat, Collection $data): void
    {
        $mess = self::SETUP_COMPLETE . "\n" . "\n" . self::YOUR_SETTINGS . "\n" . "\n" . self::PREFERRED_CARS . "\n" . "\n";
        $car_price_high = 0;

        $lastMessId = $chat->storage()->get('message_id');
        $car_brand =  $this->appendToMess('car_brand_text', 'Бренд машины:', $mess, $chat);
        $car_model_id = $this->appendToMess('car_model_id', 'Модель машины:', $mess, $chat);

        //change logic
        $car_price_low = $chat->storage()->get('car_price_low') ? $chat->storage()->get('car_price_low') : 0;

        if ($data->get("car_price_high")) {
            $car_price_high = $data->get("car_price_high");
            $chat->storage()->set('car_price_high', $car_price_high);
            $mess .= "*Ценовой диапозон:*\n " . $car_price_low . " - " . $car_price_high . "\n";
        }

        $mess .= "Чтобы подписаться на рассылку, воспользуйтесь командой /search или кнопкой 🔍 Начать поиск";
        $kb = $this->paginationKb->addPaginationToKb(Keyboard::make(), "set_car_price");
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();

    }
}
