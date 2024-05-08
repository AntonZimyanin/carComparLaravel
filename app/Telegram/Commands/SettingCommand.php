<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\SettingKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Redis;

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
    public function sendCommand(TelegraphChat $chat): void
    {
//        Redis::flushAll();
//        if (Redis::hGetAll("path")) {
//            Redis::del("path");
//        }
//
//        if ($chat->storage()->get("car_model_name") ||  $chat->storage()->get("car_brand_name")) {
//            $chat->storage()->forget("car_model_name");
//            $chat->storage()->forget("car_brand_name");
//            $chat->storage()->forget("car_price_low");
//            $chat->storage()->forget("car_price_high");
//        }
//        $lastMessId = $chat->storage()->get('message_id');
//
//        if ($lastMessId) {
//            $chat->deleteMessage($lastMessId)->send();
//        }


        $messId = $chat->message(self::mess)->keyboard(
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

        $chat->edit($lastMessId)->message(self::mess)->keyboard(
            ($this->kb)($chat->id)
        )->send();
    }

    public function editKb(TelegraphChat $chat): void
    {
        $lastMessId = $chat->storage()->get('message_id');



        $chat->replaceKeyboard(
            $lastMessId,
            ($this->kb)($chat->id)
        )->send();
    }
}
