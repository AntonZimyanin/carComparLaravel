<?php

namespace App\Telegram\Commands;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\FSM\StateManager;
use App\Telegram\Keyboards\ParsedCarsKb;
use App\Telegram\Message\CarPrefMessage;
use App\Telegram\Parser\AvBy\AvByParser;
use App\Telegram\Traits\SetPreference;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Redis;

class Search
{
    use SetPreference;
    public function __construct(
        protected AvByParser $parser,
        protected CarPreferenceController $carPrefController,
        protected AvByCarProperty $property,
        protected CarPrefMessage $carPrefMessage,
        protected ParsedCarsKb $parsedCarsKb
    ) {
    }
    /**
     * @throws StorageException
     */
    public function search(TelegraphChat $chat, StateManager $state): void
    {
        $lastMessId = $chat->storage()->get('message_id');

        $chat->deleteKeyboard($lastMessId)->send();
        $carProperty = $state->getAllData();
        $this->setPreference($carProperty, $chat->id, $this->property);

        $this->parser->set(
            $this->property,
        );
        $chat->message("Поиск начат...")->send();
        $this->parser->parse($chat);

        $lCaseBrand = strtolower($this->property->carBrand);
        $car = Redis::hGetAll("car:{$lCaseBrand}:0");
        $carCount = Redis::get('car_count');

        if (empty($car)) {
            $chat->message('Машин по заданным параметрам не найдено')->send();
            return;
        }
        $kb = $this->parsedCarsKb->get(
            0,
            $carCount,
            $lCaseBrand
        );

        $messId = $chat->message(
            $this->carPrefMessage->get($car)
        )->keyboard($kb)->send()->telegraphMessageId();

        $chat->storage()->set('car_list_message_id', $messId);
    }

    public function searchKb(TelegraphChat $chat, int $searchId): void
    {
        $pref = $this->carPrefController->get($chat->id, $searchId);

        $this->property->set(
            $chat->id,
            $pref['car_brand'],
            $pref['car_model'],
            $pref['car_price_low'],
            $pref['car_price_high'],
        );

        $this->parser->set(
            $this->property,
        );

        $chat->message("Поиск начат...")->send();

        $this->parser->parse($chat);
        $car = Redis::hGetAll("car:{$pref['car_brand']}:0");
        $carCount = Redis::get('car_count');

        if (empty($car)) {
            $chat->message('Машин по заданным параметрам не найдено')->send();
            return;
        }

        $kb = $this->parsedCarsKb->get(
            0,
            $carCount,
            $pref['car_brand']
        );
        $messId = $chat->message(
            $this->carPrefMessage->get($car)
        )->keyboard($kb)->send()->telegraphMessageId();

        $chat->storage()->set('car_list_message_id', $messId);
    }




}
