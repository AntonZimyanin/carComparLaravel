<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\SettingKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class SettingCommand
{
    private SettingKb $kb;
    private string $mess;

    public function __construct(SettingKb $kb)
    {
        $this->kb = $kb;
        $this->mess = "
*Настройки*\n
Добавление нескольких фильтров позволит Вам создавать комбинации из разных параметров.\n
👁 - посмотреть текущую настройку
⚙ - настроить фильтр
📑 - создать копию фильтра
❌ - удалить фильтр
";
    }

    /**
     * @throws StorageException
     */
    public function sendCommand(TelegraphChat $chat): void
    {
        $kb = $this->kb->getSettings();

        if ($chat->storage()->get('message_id')) {
            $this->backToSettings($chat);
            return;
        }

        $messId = $chat->message($this->mess)->keyboard(
            $kb
        )->send()->telegraphMessageId();
        $chat->storage()->set('message_id', $messId);
    }

    /**
     * @throws StorageException
     */
    public function backToSettings(TelegraphChat $chat): void
    {
        $kb = $this->kb->getSettings();
        $lastMessId = $chat->storage()->get('message_id');

        $chat->edit($lastMessId)->message($this->mess)->keyboard(
            $kb
        )->send()->telegraphMessageId();
    }
}
