<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;

class StartKb
{
    public function __invoke(): ReplyKeyboard
    {
        return ReplyKeyboard::make()
            ->row([
                ReplyButton::make('⚙️ Настройки'),
            ])
            ->row([
                ReplyButton::make('🔍 Начать поиск'),
                ReplyButton::make('ℹ️ Справка')
            ])->resize();
    }
}
