<?php

namespace App\Telegram\Traits;

use App\Telegram\FSM\StateManager;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;


trait ShowPreference
{
    public string $mess = '*Ваши настройки️*:' . "\n\n";

    private function appendToMess(mixed $value, string $label, string &$mess): void
    {
        if ($value !== null) {
            $mess .= "*$label*\n$value\n";
        }
    }

    public function showCachePref(TelegraphChat $chat, Collection $data, StateManager $state) : string
    {
        $twinSep = "\n\n";
        $mess = '*Ваши настройки️*:' . "\n\n";


        $carBrand = $state->getData($this->carFSM->carBrand);
        $carModel = $state->getData($this->carFSM->carModel);

        $this->appendToMess($carBrand, 'Бренд машины:', $mess);
        $this->appendToMess($carModel, 'Модель машины:', $mess);

        //change logic
        $carPriceLow = null ?? 0;

        if ($carPriceLow === 0) {
            $state->forgetState($this->carFSM->carPriceLow);
        }
        $carPriceHigh = $data->get("car_price_high") ?? $state->getData($this->carFSM->carPriceHigh);
        //TODO: check only data store, 'cause you store the $carPriceHigh in the main class
        if ($carPriceHigh) {
            $state->setData($this->carFSM->carPriceHigh, $carPriceHigh);
            $mess .= "*Ценовой диапозон:*\n " . $carPriceLow . " - " . $carPriceHigh . "\n";
        }
        return $mess;
    }

    public function showDBPref(TelegraphChat $chat, int $prefId) : string
    {
        $pref = $this->carPrefController->get($chat->id, $prefId);

        if ($pref['car_brand']) {
            $this->mess .= "*Бренд машины:*\n{$pref['car_brand']}\n";
        }
        if ($pref['car_model']) {
            $this->mess .= "*Модель машины:*\n{$pref['car_model']}\n";
        }
        if ($pref['car_price_high']) {
            $carPriceHigh = $pref['car_price_high'];
        }
        else {
            $carPriceHigh = '∞';
        }
        $this->mess .= "*Ценовой диапозон:*\n{$pref['car_price_low']} - $carPriceHigh\n";
        if ($pref['car_price_low']) {
        }

        $this->mess .= "Чтобы найти нужные вам машины кликните назад и выберите фильтр, котрый вам нужен, нажав на кнопку 🔍\nИли настройте новый /settings";

        return $this->mess;
    }
}
