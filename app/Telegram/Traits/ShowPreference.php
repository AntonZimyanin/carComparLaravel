<?php

namespace App\Telegram\Traits;

use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;


trait ShowPreference
{
    public string $mess =  '*Настройка завершена!*' . "\n\n" . 'Ваши настройки️:' . "\n\n";

    private function appendToMess(string $key, string $label, TelegraphChat $chat): void
    {
        $value = $chat->storage()->get($key);
        if ($value !== null) {
            $this->mess .= "*$label*\n$value\n";
        }
    }

    public function showCachePref(TelegraphChat $chat, Collection $data) : string
    {
        $this->appendToMess('car_brand_name', 'Бренд машины:', $chat);
        $this->appendToMess('car_model_name', 'Модель машины:', $chat);

        //change logic
        $carPriceLow = $chat->storage()->get('car_price_low') ?: 0;

        if ($carPriceLow === 0) {
            $chat->storage()->forget('car_price_state');
        }

        if ($data->get("car_price_high") || $chat->storage()->get("car_price_high")) {
            $carPriceHigh = $data->get("car_price_high") ?? $chat->storage()->get("car_price_high");
            $chat->storage()->set('car_price_high', $carPriceHigh);
            $this->mess .= "*Ценовой диапозон:*\n " . $carPriceLow . " - " . $carPriceHigh . "\n";
        }

        $this->mess .= "Чтобы найти нужные вам машины, воспользуйтесь командой /search или кнопкой 🔍 Начать поиск\nНажмите /store ⬇️, чтобы сохранить фильтр";

        return $this->mess;
    }

    public function showDBPref(TelegraphChat $chat, int $searchId) : string 
    {
        $pref = $this->carPrefController->get($chat->id, $searchId);

        if ($pref->car_brand !== null) {
            $this->mess .= "*Бренд машины:*\n$pref->car_brand_name\n";
        } 
        if ($pref->car_model !== null) {
            $this->mess .= "*Модель машины:*\n$pref->car_model_name\n";
        }
        if ($pref->car_price_high === null) { 
            $carPriceHigh = '∞';
        }
        else { 
            $carPriceHigh = $pref->car_price_hig;
        }
        if ($pref->car_price_low !== null) {
            $this->mess .= "*Ценовой диапозон:*\n$pref->car_price_low - $pref->car_price_high\n";
        }

        $this->mess .= "Чтобы найти нужные вам машины кликните назад и выберите фильтр, котрый вам нужен, нажав на кнопку 🔍\nИли настройте новый /settings";

        return $this->mess;
    }
}
