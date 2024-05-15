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

    public function edit(TelegraphChat $chat, Collection $data): void
    {
        $prefId = $data->get('filter_id');
        $lastMessId = $chat->storage()->get('message_id');

        $kb = Keyboard::make()->row([
            Button::make('Изменить настройки')->action('add_filter'),
        ]);

        $mess = $this->showDBPref($chat, $prefId);

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();

    }

    public function show(TelegraphChat $chat, Collection $data) {

        $prefId = $data->get('filter_id');
        $lastMessId = $chat->storage()->get('message_id');

        $mess = $this->showDBPref($chat, $prefId);

        $kb = Keyboard::make()->row([
                Button::make('Назад')->action('back_to_settings'),
            ]);

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
