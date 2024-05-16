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

use App\Telegram\KeyboardActions\ShowParsedCars;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Stringable;

class Handler extends WebhookHandler
{
    public function __construct(
        protected StartCommand        $startCommand,
        protected SettingCommand      $settingCommand,
        protected AddFilter           $addFilter,
        protected SetCarBrand         $setCarBrand,
        protected SetCarModel         $setCarModel,
        protected SetCarPrice         $setCarPrice,
        protected ShowCars            $showCars,
        protected Search              $search,
        protected FilterAction        $filterAction,
        protected CarPriceManualInput $carPriceManualInput,
        protected HelpCommand         $helpCommand,
        protected StoreCommand        $storeCommand,
        protected SetSort             $setSort,
        protected CarFSM              $carFSM,
        protected StateManager        $state,
        protected ShowParsedCars $showParsedCars
    ) {
        parent::__construct();
        // $this->bot = TelegraphBot::create([
        //     'token' => $_ENV['BOT_TOKEN'],
        //     'name' => $_ENV['BOT_NAME'],
        // ]);
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

    /**
     * @throws StorageException
     */
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

    public function page_number() : void{
    }

    public function show_parsed_cars(): void
    {
        $this->showParsedCars->handle(
            $this->chat,
            $this->data
        );
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
            $this->chat,
            $this->state
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
            $this->{$action[$messageText]}();
            return;
        }
        // state can be the array[0] or bool
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
