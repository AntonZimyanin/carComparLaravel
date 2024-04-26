<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\StartKb;

use DefStudio\Telegraph\Models\TelegraphChat;

class StartCommand
{
    private StartKb $kb;
    public function __construct(StartKb $kb)
    {
        $this->kb = $kb;
    }

    public function sendCommand(TelegraphChat $chat): void
    {

        $chat->message('*Привет!*')->replyKeyboard(
            $this->kb->getStartKb()
        )->send();
    }
}
