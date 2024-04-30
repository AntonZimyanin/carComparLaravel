<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\StartKb;

use DefStudio\Telegraph\Models\TelegraphChat;

class StartCommand
{
    private StartKb $kb;

    const mess = "
Добро пожаловать!

Я БОТ, который поможет купить машину

Я помогу купить машину по Вашим индивидуальным параметрам⚙️

Больше не нужно тратить время⏳ на поиски новых объявлений. Я сделаю это за Вас😉

Чтобы начать перейдите в ⚙️Настройки для выбора параметров машины.
—————————————•
Если у Вас нет кнопок, нажмите на квадратик возле микрофона
";
    public function __construct(StartKb $kb)
    {
        $this->kb = $kb;
    }

    public function sendCommand(TelegraphChat $chat): void
    {

        $chat->message(self::mess)->replyKeyboard(
            $this->kb->getStartKb()
        )->send();
    }
}
