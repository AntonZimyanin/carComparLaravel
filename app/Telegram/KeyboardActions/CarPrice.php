<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Traits\ShowPreference;

use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class CarPrice
{
    use ShowPreference;
    private PaginationKb $pagination;

    public function __construct(PaginationKb $paginationKb)
    {
        $this->pagination = $paginationKb;
    }
    public const SETUP_COMPLETE = '*Настройка завершена!*';
    public const YOUR_SETTINGS = 'Ваши настройки️:';
    public const PREFERRED_CARS = 'Предпочитаемые машины:';


    /**
     * @throws StorageException
     */
    public function setCarPrice(TelegraphChat $chat, Collection $data): void
    {
        // $twinSep = "\n\n";
        // $mess = self::SETUP_COMPLETE . $twinSep . self::YOUR_SETTINGS . $twinSep . self::PREFERRED_CARS . $twinSep;

        $lastMessId = $chat->storage()->get('message_id');
        // $this->appendToMess('car_brand_name', 'Бренд машины:', $mess, $chat);
        // $this->appendToMess('car_model_name', 'Модель машины:', $mess, $chat);

        // //change logic
        // $carPriceLow = $chat->storage()->get('car_price_low') ?: 0;

        // if ($carPriceLow === 0) {
        //     $chat->storage()->forget('car_price_state');
        // }

        // if ($data->get("car_price_high") || $chat->storage()->get("car_price_high")) {
        //     $carPriceHigh = $data->get("car_price_high") ?? $chat->storage()->get("car_price_high");
        //     $chat->storage()->set('car_price_high', $carPriceHigh);
        //     $mess .= "*Ценовой диапозон:*\n " . $carPriceLow . " - " . $carPriceHigh . "\n";
        // }

        $mess = $this->showCachePref($chat, $data);
        $kb = $this->pagination->addPaginationToKb(Keyboard::make(), "set_car_price", "back_to_settings");

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();

    }
}
