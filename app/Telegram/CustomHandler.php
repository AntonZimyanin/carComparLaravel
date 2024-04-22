<?php

namespace App\Telegram;


use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\SettingCommand;

use App\Telegram\KeyboardActions\Filter;
use App\Telegram\KeyboardActions\CarBrand;
use App\Telegram\KeyboardActions\CarModel;
use App\Telegram\KeyboardActions\CarPrice;


use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;


class CustomHandler extends WebhookHandler
{
    public function help(): void
    {
        $this->reply("I will help you");
    }

}