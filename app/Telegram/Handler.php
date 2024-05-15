<?php

namespace App\Telegram;

use App\Telegram\FSM\CarFSM;
use App\Telegram\FSM\StateManager;

use App\Telegram\Commands\HelpCommand;
use App\Telegram\Commands\Search;
use App\Telegram\Commands\SetSort;
use App\Telegram\Commands\SettingCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\StoreCommand;
use App\Telegram\KeyboardActions\SetCarBrand;
use App\Telegram\KeyboardActions\SetCarModel;
use App\Telegram\KeyboardActions\SetCarPrice;
use App\Telegram\KeyboardActions\AddFilter;
use App\Telegram\KeyboardActions\FilterAction\FilterAction;
use App\Telegram\KeyboardActions\ShowCars;
use App\Telegram\KeyboardActions\CarPriceManualInput;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Stringable;

class Handler extends WebhookHandler
{
    //FSM
    private CarFSM $carFSM;
    private StateManager $state;

    //commands
    private StartCommand $startCommand;
    private HelpCommand $helpCommand;
    private SettingCommand $settingCommand;
    private Search $search;
    private StoreCommand $storeCommand;
    private SetSort $setSort;

    //action
    private CarPriceManualInput $carPriceManualInput;
    private AddFilter $addFilter;
    private SetCarBrand $setCarBrand;
    private SetCarModel $setCarModel;
    private SetCarPrice $setCarPrice;
    private ShowCars $showCars;
    private FilterAction $filterAction;

    public function __construct(
        StartCommand        $startCommand,
        SettingCommand      $settingCommand,
        AddFilter           $addFilter,
        SetCarBrand         $setCarBrand,
        SetCarModel         $setCarModel,
        SetCarPrice         $setCarPrice,
        ShowCars            $showCars,
        Search              $search,
        FilterAction        $filterAction,
        CarPriceManualInput $carPriceManualInput,
        HelpCommand         $helpCommand,
        StoreCommand        $storeCommand,
        SetSort             $setSort,
        CarFSM              $carFSM,
        StateManager        $state
    ) {
        parent::__construct();
        // $this->bot = TelegraphBot::create([
        //     'token' => $_ENV['BOT_TOKEN'],
        //     'name' => $_ENV['BOT_NAME'],
        // ]);
        $this->startCommand = $startCommand;
        $this->settingCommand = $settingCommand;

        $this->addFilter = $addFilter;
        $this->setCarBrand = $setCarBrand;
        $this->setCarModel = $setCarModel;
        $this->setCarPrice = $setCarPrice;
        $this->showCars = $showCars;
        $this->search = $search;
        $this->filterAction = $filterAction;
        $this->carPriceManualInput = $carPriceManualInput;
        $this->helpCommand = $helpCommand;
        $this->storeCommand = $storeCommand;
        $this->setSort = $setSort;
        $this->carFSM = $carFSM;
        $this->state = $state;

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
            $this->state,
        );
    }

    public function set_sort(): void
    {
        $this->setSort->get($this->chat);
    }

    public function set_sort_action()
    {
        //set sort
        $sort = $this->data->get('sort');
        $messSortId = $this->chat->storage()->get('sort_message_id');
        $this->chat->message($sort)->send();
        //        $this->chat->edit($messSortId)->message("Сортировка успешно установлена")->send();
    }

    public function filer_data()
    {
        $this->filterAction->show(
            $this->chat,
            $this->data,
        );
    }

    /**
     * @throws StorageException
     */
    public function add_filter(): void
    {
        $this->addFilter->handle(
            $this->chat,
            $this->state
        );
    }

    /**
     * @throws StorageException
     */
    public function show_cars(): void
    {
        $this->showCars->showCars(
            $this->chat,
            $this->data,
            $this->state
        );
    }

    /**
     * @throws StorageException
     */
    public function set_car_brand(): void
    {
        $this->setCarBrand->handle(
            $this->chat,
            $this->data,
            $this->state
        );
    }

    /**
     * @throws StorageException
     */
    public function set_car_model(): void
    {
        $this->setCarModel->handle(
            $this->chat,
            $this->data,
            $this->state
        );
    }

    /**
     * @throws StorageException
     */
    public function set_car_price(): void
    {
        $this->setCarPrice->handle(
            $this->chat,
            $this->data,
            $this->state
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
        $searchId = $this->data->get('search_id');
        if ($searchId) {
            $this->search->searchKb(
                $this->chat,
                $searchId
            );
        } else {
            $this->search->search(
                $this->chat,
                $this->state,
            );
        }
    }

    public function show_parse_cars(): void
    {
        $lastMessId = $this->chat->storage()->get('car_list_message_id');
        $carId = $this->data->get('car_id');
        $carBrand = $this->data->get('brand');

        $car =  Redis::hGetAll("car:{$carBrand}:$carId");
        $carCount = Redis::get('car_count');

        $pageNumber = $carId + 1;
        $kb = Keyboard::make()->row([
            Button::make("{$pageNumber}/$carCount")->action('page_number')->param('id', 0),
        ])
            ->row([
                Button::make('Назад')->action('show_parse_cars')->param('car_id', $carId - 1)->param('brand', $carBrand),
                Button::make('Вперед')->action('show_parse_cars')->param('car_id', $carId + 1)->param('brand', $carBrand),
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

    public function edit_filter()
    {
        $this->filterAction->edit(
            $this->chat,
            $this->data
        );
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
        if ($this->state->getState($this->carFSM->carPriceLow) && $messageText) {
            $this->carPriceManualInput->handle(
                $this->chat,
                $this->data,
                $this->state,
                $messageText
            );
            return;
        }

        $this->chat->message("Такой команды нет")->send();
    }
}
