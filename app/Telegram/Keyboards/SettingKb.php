<?php

namespace App\Telegram\Keyboards;

use App\Http\Controllers\CarPreferenceController;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class SettingKb
{
    private CarPreferenceController $carPrefController;
    public function __construct(CarPreferenceController $carPrefController)
    {
        $this->carPrefController = $carPrefController;
    }
    public function getSettings(int $chatId = null): Keyboard
    {
        $kb = Keyboard::make()
            ->row([
                Button::make('Добавить фильтр')->action('add_filter')
            ]);


        $pref = $this->carPrefController->index($chatId);
        if (!empty($pref)) {
            foreach ($pref as $p) {
                $kb->row([
                    Button::make($p['car_brand'] . ' ' . $p['car_model'])->action('filter_page'),
                ])
                ->row([
                    Button::make('⚙️')->action('change_filter')->param('chat_id', $p['id']),
                    Button::make('©️')->action('copy_filter')->param('chat_id', $p['id']),
                    Button::make('❌')->action('delete_filter')->param('chat_id', $p['id']),
                ]);
            }
        }

        return $kb;
    }
}
