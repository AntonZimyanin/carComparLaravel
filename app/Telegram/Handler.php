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
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Redis;
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
        Search $search,

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

    public function show_parse_cars() : void {
        $lastMessId = $this->chat->storage()->get('message_id');
        $carId = $this->data->get('id');

        $car = Redis::hGetAll("car:$carId");


        $kb = Keyboard::make()
            ->row([
                Button::make('Назад')->action('show_parse_cars')->param('id', 0),
                Button::make('Впред')->action('show_parse_cars')->param('id', 1),
            ]);
        $this->chat->edit($lastMessId)->photo($car['photourl'])->message(
            "
Продавец: {$car['sellername']}
Город: {$car['locationname']}
Бренд: {$car['brand']}
Модель: {$car['model']}
Поколение: {$car['generation']}
Год: {$car['year']}
Цена: {$car['price']}$
Ссылка: {$car['publicurl']} "
        )->keyboard($kb)->send();


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
            '⚙️ Настройки' => 'setting',
            '🔍 Начать поиск' => 'search',
            'ℹ️ Справка' => 'help',
        ];
        if (array_key_exists($messageText, $action)) {
            $cmd = $action[$messageText];
            $this->{$cmd}();
            return;
        }

        if ($this->chat->storage()->get('car_price_state')) {
            $res = explode(' ', $messageText);
            if (count($res) == 2 && is_numeric($res[0]) && is_numeric($res[1])) {
                $this->chat->storage()->set('car_price_low', $res[0]);
                $this->chat->storage()->set('car_price_high', $res[1]);
                $this->carPrice->setCarPrice($this->chat, $this->data);
                $this->chat->message("Цена успешно установлена: $res[0] - $res[1]")->send();
                $this->chat->storage()->forget('car_price_state');
                return;
            }
            $this->chat->message("Введите ценовой диапазон в формате *от до*\nПример: 100 200")->send();
            return;
        }
        $this->chat->message("Такой команды нет")->send();
    }


}
