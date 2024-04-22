<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\SettingKb;


use DefStudio\Telegraph\Models\TelegraphChat;


class SettingCommand
{

    private SettingKb $kb;

    public function __construct(SettingKb $kb)
    {
        $this->kb = $kb;
    }
    public function sendCommand(TelegraphChat $chat): void
    {
        $mess = "
*Настройки*\n
Добавление нескольких фильтров позволит Вам создавать комбинации из разных параметров.\n
👁 - посмотреть текущую настройку
⚙ - настроить фильтр
📑 - создать копию фильтра
❌ - удалить фильтр
";
        $chat->message($mess)->keyboard(
            $this->kb->getSettings()
        )->send();
    }
}
