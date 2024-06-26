<?php

namespace App\Telegram\Commands;

use App\Telegram\FSM\StateManager;
use App\Telegram\Keyboards\SettingKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Redis;

class SettingCommand
{
    public const MESS = "
*Настройки*\n
Добавление нескольких фильтров позволит Вам создавать комбинации из разных параметров.\n
👁 - посмотреть текущую настройку
⚙ - настроить фильтр
📑 - создать копию фильтра
❌ - удалить фильтр
";

    public function __construct(
        protected SettingKb $kb)
    {
    }

    /**
     * @throws StorageException
     */
    public function sendCommand(TelegraphChat $chat, StateManager $state): void
    {
        $state->clear();
        Redis::del("path");

        $messId = $chat->message(self::MESS)->keyboard(
            ($this->kb)($chat->id)
        )->send()->telegraphMessageId();
        $chat->storage()->set('message_id', $messId);
    }

    /**
     * @throws StorageException
     */
    public function backToSettings(TelegraphChat $chat): void
    {
        $lastMessId = $chat->storage()->get('message_id');
        Redis::del("path");

        $chat->edit($lastMessId)->message(self::MESS)->keyboard(
            ($this->kb)($chat->id)
        )->send();
    }

    /**
     * @throws StorageException
     */
    public function editKb(TelegraphChat $chat): void
    {
        $lastMessId = $chat->storage()->get('message_id');

        $chat->replaceKeyboard(
            $lastMessId,
            ($this->kb)($chat->id)
        )->send();
    }
}
