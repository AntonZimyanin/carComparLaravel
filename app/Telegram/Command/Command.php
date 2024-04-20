<?php

namespace App\Telegram\Commands;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;


class Command { 

    public function start() 
    {
        $keyboard = ReplyKeyboard::make()
            ->row([
                ReplyButton::make('Настройки'),
                ReplyButton::make('Начать поиск'),
                ReplyButton::make('Справка')
            ])
            ->resize();
        return $keyboard;
    }
}