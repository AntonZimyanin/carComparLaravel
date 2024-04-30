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
    public function getSettings(int $telegramId=null): Keyboard
    {
        $kb = Keyboard::make()
            ->row([
                Button::make('Добавить фильтр')->action('add_filter')
            ]);


        for ($i = 0; $i < 1; $i++) {
            $kb->row([
                Button::make('⚙️')->action('setting'),
                Button::make('©️')->action('setting'),
                Button::make('❌')->action('setting')
            ]);
        }
//        if ($this->carPrefController->index($telegramId)->count() > 0) {
//            $len = $this->carPrefController->index($telegramId)->count();
//
//        }

        return $kb;
    }
}
