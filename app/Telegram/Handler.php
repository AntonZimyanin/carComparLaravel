<?php

namespace App\Telegram;

use App\Telegram\Commands\HelpCommand;
use App\Telegram\Commands\Search;
use App\Telegram\Commands\SettingCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\StoreCommand;
use App\Telegram\KeyboardActions\CarBrand;
use App\Telegram\KeyboardActions\CarModel;
use App\Telegram\KeyboardActions\CarPrice;
use App\Telegram\KeyboardActions\Filter;
use App\Telegram\KeyboardActions\FilterAction\FilterAction;
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
    private HelpCommand $helpCommand;
    private SettingCommand $settingCommand;
    private Search $search;
    private StoreCommand $storeCommand;

    //action
    private Filter $filter;
    private CarBrand $carBrand;
    private CarModel $carModel;
    private CarPrice $carPrice;
    private ShowCars $showCars;
    private FilterAction $filterAction;

    public function __construct(
        StartCommand $startCommand,
        SettingCommand $settingCommand,
        Filter $filter,
        CarBrand $carBrand,
        CarModel $carModel,
        CarPrice $carPrice,
        ShowCars $showCars,
        Search $search,
        FilterAction $filterAction,
        HelpCommand $helpCommand,
        StoreCommand $storeCommand,
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
        $this->filterAction = $filterAction;
        $this->helpCommand = $helpCommand;
        $this->storeCommand = $storeCommand;
    }

    /**
     * Command handler start
     */
    public function start(): void
    {
       $this->startCommand->sendCommand($this->chat);
    }

    public function help(): void
    {
        $this->helpCommand->sendCommand($this->chat);
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

    public function set_sort(): void{
        $kb = Keyboard::make()->row([
            Button::make('Актуальные')->action('set_sort_action')->param('sort', 1),
        ])->row([
            Button::make('Дешёвые')->action('set_sort_action')->param('sort', 2)
        ])->row([
            Button::make('Дорогие')->action('set_sort_action')->param('sort', 3),
        ])->row([
            Button::make('Новые объявления')->action('set_sort_action')->param('sort', 4),
        ])->row([
            Button::make('Старые объявления')->action('set_sort_action')->param('sort', 5),
        ])->row([
            Button::make('С наименьшим пробегом')->action('set_sort_action')->param('sort', 8),
        ])->row([
            Button::make('Новые по году')->action('set_sort_action')->param('sort', 6),
        ])->row([
            Button::make('Старые по году')->action('set_sort_action')->param('sort', 7),
        ]);

        $messSortId = $this->chat->message("Выбери способ сортировки машин")
            ->keyboard($kb)->send()->telegraphMessageId();
        $this->chat->storage()->set('sort_message_id', $messSortId);
    }

    public function set_sort_action()
    {
        //set sort
        $sort = $this->data->get('sort');
        $messSortId = $this->chat->storage()->get('sort_message_id');
        $this->chat->message( $sort)->send();
//        $this->chat->edit($messSortId)->message("Сортировка успешно установлена")->send();
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


    /**
     * @throws StorageException
     */
    public function back_to_settings(): void
    {
        $this->settingCommand->backToSettings(
            $this->chat,
        );
    }

    public function edit_setting_kb(): void
    {
        $this->settingCommand->editKb(
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
        $lastMessId = $this->chat->storage()->get('car_list_message_id');
        $carId = $this->data->get('id');

        $car = Redis::hGetAll("car:$carId");
        $carCount = Redis::get('car_count');

        $pageNumber = $carId + 1;
        $kb = Keyboard::make()->row([
            Button::make("{$pageNumber}/$carCount")->action('page_number')->param('id', 0),
        ])
            ->row([
                Button::make('Назад')->action('show_parse_cars')->param('id', $carId - 1),
                Button::make('Вперед')->action('show_parse_cars')->param('id', $carId + 1),
            ]);

        $this->chat->edit($lastMessId)->message(
            "
Продавец: {$car['sellername']}
Город: {$car['locationname']}
Бренд: {$car['brand']}
Модель: {$car['model']}
Поколение: {$car['generation']}
Год: {$car['year']}
Цена: {$car['price']}$
Ссылка: {$car['publicurl']}"
        )->keyboard($kb)->send();
    }

    /**
     * @throws StorageException
     */
    public function delete_filter(): void
    {
        $this->filterAction->del(
            $this->chat->id,
            $this->data
        );
        $this->edit_setting_kb();
    }

    public function copy_filter()
    {
        $this->chat->message("copy_")->send();
//        $this->filterAction->copy(
//            $this->chat,
//            $this->data
//        );
    }

    public function edit_filter()
    {
        $this->chat->message("edit")->send();
//        $this->filterAction->edit(
//            $this->chat,
//            $this->data
//        );
    }


    protected function handleUnknownCommand(Stringable $text): void
    {
        Telegraph::message("Такой команды нет")->send();
    }

    /**
     * @throws StorageException
     */
    public function store(): void
    {
        $this->storeCommand->store(
            $this->chat
        );

    }
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
