<?php


namespace App\Telegram\Commands;

use App\Telegram\Keyboards\StarKb;

use DefStudio\Telegraph\Models\TelegraphChat;


class StartCommand
{
    private StarKb $kb;
    public function __construct(StarKb $kb)
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
