<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\SettingKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class SettingCommand
{
    private SettingKb $kb;
    const mess = "
*Настройки*\n
Добавление нескольких фильтров позволит Вам создавать комбинации из разных параметров.\n
👁 - посмотреть текущую настройку
⚙ - настроить фильтр
📑 - создать копию фильтра
❌ - удалить фильтр
";

    public function __construct(SettingKb $kb)
    {
        $this->kb = $kb;
    }

    /**
     * @throws StorageException
     */
    public function sendCommand($chat): void
    {
        $kb = $this->kb->getSettings($chat->id);

        $messId = $chat->message(self::mess)->keyboard(
            $kb
        )->send()->telegraphMessageId();
        $chat->storage()->set('message_id', $messId);
    }

    /**
     * @throws StorageException
     */
    public function backToSettings(TelegraphChat $chat): void
    {
        $kb = $this->kb->getSettings($chat->id);
        $lastMessId = $chat->storage()->get('message_id');

        $chat->edit($lastMessId)->message(self::mess)->keyboard(
            $kb
        )->send();
    }
}
