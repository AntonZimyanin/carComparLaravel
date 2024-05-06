<?php

namespace App\Telegram;

use App\Http\Controllers\CarPreferenceController;
use App\Http\Controllers\UserController;

use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\SettingCommand;

use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\KeyboardActions\Search;

use App\Telegram\KeyboardActions\Filter;
use App\Telegram\KeyboardActions\CarBrand;
use App\Telegram\KeyboardActions\CarModel;
use App\Telegram\KeyboardActions\CarPrice;
use App\Telegram\KeyboardActions\ShowCars;

use Illuminate\Support\Facades\Redis;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;

class Handler extends WebhookHandler
{
    //contoollers
    private CarPreferenceController $carPreferenceController;

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

        CarPreferenceController $carPreferenceController
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

        $this->carPreferenceController = $carPreferenceController;
    }

    /**
     * Command handler start
     */
    public function start(): void
    {
//        Redis::rpush('name', 'Taylor');
        Redis::set('carcompar:1:name', '[1, 2, 4, 3]');
//        Redis::set('carcompar:2:name', 'Taylor');
//        Redis::set('carcompar:3:name', 'Taylor');

//        Redis::lpush('list', 2, 3, 5);
//        Redis::lpush('list', 1, 3, 5);


        for ($i = 1; $i <= 3; $i++) {
            $v = (array)Redis::get("carcompar:$i:name");
            $this->chat->message("Hello, {$v[0]}")->send();
        }


//        $v = Redis::lrange('name', 0, 1);
//        $redis->client()->set('name', 'Taylor');
//        Redis::set('name', 'Taylor');
        $this->startCommand->sendCommand($this->chat);
        $chat_id = $this->chat->id;

        $data = $this->carPreferenceController->index($chat_id);
//        $this->chat->message("Hello, { $v }")->send();
        $this->chat->storage()->set('chat_id',  $chat_id);
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
