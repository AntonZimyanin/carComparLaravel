<?php

namespace App\Telegram;


use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\SettingCommand;

use App\Telegram\KeyboardActions\Filter;
use App\Telegram\KeyboardActions\CarBrand;
use App\Telegram\KeyboardActions\CarModel;
use App\Telegram\KeyboardActions\CarPrice;


use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;


class Handler extends WebhookHandler
{
    //commands
    private StartCommand $startCommand;
    private SettingCommand $settingCommand;

    //action 
    private Filter $filter;
    private CarBrand $carBrand;
    private CarModel $carModel;
    private CarPrice $carPrice;


    public function __construct(
        StartCommand $startCommand,
        SettingCommand $settingCommand,
        Filter $filter,
        CarBrand $carBrand,
        CarModel $carModel,
        CarPrice $carPrice,
    ) {
        parent::__construct();
        $this->startCommand = $startCommand;
        $this->settingCommand = $settingCommand;

        $this->filter = $filter;
        $this->carBrand = $carBrand;
        $this->carModel = $carModel;
        $this->carPrice = $carPrice;
    }

    public function start(): void
    {
        $this->startCommand->sendCommand($this->chat);
    }

    public function setting(): void
    {
        $this->settingCommand->sendCommand($this->chat);
    }

    public function add_filter(): void
    {
        $this->filter->addFilter($this->chat);
    }
    public function set_car_brand(): void
    {
        $this->carBrand->setCarBrand(
            $this->chat,
            $this->bot->storage()
        );
    }

    public function set_car_model(): void
    {
        $this->carModel->setCarModel(
            $this->chat,
            $this->bot->storage()
        );
    }


    public function set_car_price(): void
    {
        $this->carPrice->setCarPrice(
            $this->chat,
            $this->bot->storage()
        );
    }


    public function help(): void
    {
        $this->reply("I will help you");
    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        if ($text->value() === 'Настройки') {
            $this->setting();
        }
        if ($text->value() === 'Начать поиск') {
            // $this->add_filter();
        }
        if ($text->value() === 'Справка') {
            $this->help();
        }
    }
}
