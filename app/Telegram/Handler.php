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

    /**
     * @throws StorageException
     */
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

        if ($this->chat->storage()->get('car_price_state') === false) {
            $res = explode(' ', $messageText);
            if (count($res) == 2 && is_numeric($res[0]) && is_numeric($res[1])) {

                $this->chat->storage()->set('car_price_low', $res[0]);
                $this->chat->storage()->set('car_price_high', $res[1]);
                $this->carPrice->setCarPrice($this->chat, $this->data,);
                return;
            }
            Telegraph::message("Введите ценовой диапазон в формате *от до*\nПример: 100 200")->send();
        }

        Telegraph::message("Такой команды нет")->send();



    }


}
