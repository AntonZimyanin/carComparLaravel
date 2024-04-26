<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

class StartKb
{
    public function getStartKb(): ReplyKeyboard
    {
        return ReplyKeyboard::make()
            ->row([
                ReplyButton::make('Настройки'),
            ])
            ->row([
                ReplyButton::make('Начать поиск'),
                ReplyButton::make('Справка')
            ])->resize();
    }
}
