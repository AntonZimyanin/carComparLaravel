<?php

namespace App\Telegram\Commands;

use DefStudio\Telegraph\Models\TelegraphChat;

class HelpCommand
{
    const mess = "
Я БОТ, который поможет купить машину

Я помогу купить машину по Вашим индивидуальным параметрам⚙️

Больше не нужно тратить время⏳ на поиски новых объявлений. Я сделаю это за Вас😉

Чтобы начать перейдите в ⚙️Настройки для выбора параметров машины.
—————————————•
Если у Вас нет кнопок, нажмите на квадратик возле микрофона
";
    public function sendCommand(TelegraphChat $chat): void
    {
        $chat->message(self::mess)->send();
    }
}
