<?php

namespace App\Telegram;

use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\SettingCommand;


use App\Telegram\KeyboardActions\Search;

use App\Telegram\KeyboardActions\Filter;
use App\Telegram\KeyboardActions\CarBrand;
use App\Telegram\KeyboardActions\CarModel;
use App\Telegram\KeyboardActions\CarPrice;
use App\Telegram\KeyboardActions\ShowCars;

use DefStudio\Telegraph\Exceptions\StorageException;
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
    private ShowCars $showCars;
    private Search $search;

    public function __construct(
        StartCommand $startCommand,
        SettingCommand $settingCommand,
        Filter $filter,
        CarBrand $carBrand,
        CarModel $carModel,
        CarPrice $carPrice,
        ShowCars $showCars,
        Search $search
    ) {
        parent::__construct();
        $this->startCommand = $startCommand;
        $this->settingCommand = $settingCommand;

        $this->filter = $filter;
        $this->carBrand = $carBrand;
        $this->carModel = $carModel;
        $this->carPrice = $carPrice;
        $this->showCars = $showCars;
        $this->search = $search;
    }

    /**
     * Command handler start
     */
    public function start(): void
    {
//        $this->chat->photo("https://avcdn.av.by/advertmedium/0002/7126/7850.jpg")
//            ->message("Привет! Я помогу тебе найти автомобиль")
//            ->send();
        $this->startCommand->sendCommand($this->chat);
    }

    /**
     * @throws StorageException
     */
    public function setting(): void
    {
        $this->settingCommand->sendCommand(
            $this->chat,
        );
    }

    /**
     * @throws StorageException
     */
    public function add_filter(): void
    {
        $this->filter->addFilter($this->chat);
    }

    /**
     * @throws StorageException
     */
    public function show_cars(): void
    {
        $this->showCars->showCars(
            $this->chat,
            $this->data
        );
    }

    /**
     * @throws StorageException
     */
    public function set_car_brand(): void
    {
        $this->carBrand->setCarBrand(
            $this->chat,
            $this->data,
        );
    }

    /**
     * @throws StorageException
     */
    public function set_car_model(): void
    {
        $this->carModel->setCarModel(
            $this->chat,
            $this->data,
        );
    }


    /**
     * @throws StorageException
     */
    public function set_car_price(): void
    {
        $this->carPrice->setCarPrice(
            $this->chat,
            $this->data,
        );
    }

    public function help(): void
    {
        $this->reply("I will help you");
    }


    /**
     * @throws StorageException
     */
    public function back_to_settings(): void
    {
        $this->settingCommand->backToSettings(
            $this->chat,
        );
    }

    /**
     * @throws StorageException
     */
    public function search(): void
    {
        $this->search->search(
            $this->chat,
        );

    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        Telegraph::message("Такой команды нет")->send();
    }
    protected function handleChatMessage(Stringable $text): void
    {
        $messageText = $text->value();
        $action = [
            'Настройки' => 'setting',
            'Начать поиск' => 'search',
            'Справка' => 'help',
        ];


         if (array_key_exists($messageText, $action)) {
             $cmd = $action[$messageText];
             $this->{$cmd}();
             return;
         }

         Telegraph::message("Такой команды нет")->send();
    }


}
