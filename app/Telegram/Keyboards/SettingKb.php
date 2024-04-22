<?php


namespace App\Telegram\Keyboards;


use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;


class SettingKb
{

    public function getSettings(): Keyboard
    {
        return Keyboard::make()
            ->row([
                Button::make('Добавить фильтр')->action('add_filter')
            ]);
    }
}
