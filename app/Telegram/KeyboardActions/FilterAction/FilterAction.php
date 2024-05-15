<?php

namespace App\Telegram\KeyboardActions\FilterAction;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Traits\ShowPreference;

use Illuminate\Support\Collection;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\Button;


class FilterAction
{
    use ShowPreference;
    private CarPreferenceController $carPrefController;
    public function __construct(CarPreferenceController $carPrefController)
    {
        $this->carPrefController = $carPrefController;
    }

    public function del($chatId, Collection $data): void
    {
        $prefId = $data->get('pref_id');
        $this->carPrefController->destroy($chatId, $prefId);
    }
    public function copy($chatId, Collection $data): void
    {
        $prefId = $data->get('pref_id');
        $this->carPrefController->copy($chatId, $prefId);
    }

    public function edit($chatId, Collection $data): void
    {
        $prefId = $data->get('pref_id');
        $this->carPrefController->update($chatId, $prefId);
    }

    public function show(TelegraphChat $chat, Collection $data) { 
        $lastMessId = $chat->storage()->get('message_id');
        $prefId = $data->get('filter_id');
        $pref = $this->carPrefController->get($chat->id, $prefId);

        $mess = '';
        $chat->message("asdkfjn")->send();
        if ($pref['car_brand']) {
            $mess .= "*Бренд машины:*\n{$pref['car_brand']}\n";
        } 
        if ($pref['car_model']) {
            $mess .= "*Модель машины:*\n{$pref['car_model']}\n";
        }
        if ($pref['car_price_high']) { 
            $carPriceHigh = '∞';
        }
        else { 
            $carPriceHigh = $pref['car_price_high'];
        }
        $mess .= "*Ценовой диапозон:*\n{$pref['car_price_low']} - $carPriceHigh\n";
        if ($pref['car_price_low']) {
        }

        $mess .= "Чтобы найти нужные вам машины кликните назад и выберите фильтр, котрый вам нужен, нажав на кнопку 🔍\nИли настройте новый /settings";

        $kb = Keyboard::make()->row([
                Button::make('Назад')->action('back_to_settings'),
            ]);

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
