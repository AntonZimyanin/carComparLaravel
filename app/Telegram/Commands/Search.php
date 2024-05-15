<?php

namespace App\Telegram\Commands;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\FSM\StateManager;
use App\Telegram\Parser\AvBy\AvByParser;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Redis;

class Search
{
    private AvByParser $parser;
    private CarPreferenceController $carPrefController;

    private AvByCarProperty $property;

    public function __construct(
        AvByParser $parser,
        CarPreferenceController $carPrefController,
        AvByCarProperty $property,
    ) {
        $this->parser = $parser;
        $this->carPrefController = $carPrefController;
        $this->property = $property;
    }
    /**
     * @throws StorageException
     */
    public function search(TelegraphChat $chat, StateManager $state): void
    {
        $lastMessId = $chat->storage()->get('message_id');

        $chat->deleteKeyboard($lastMessId)->send();

        $carProperty = $state->getAllData();

//        $carModelName = $chat->storae()->get("car_model_name") ?? '';
//        $carBrandName = $chat->storgage()->get('car_brand_name') ?? '';
//        $carPriceLow = (int)$chat->storage()->get('car_price_low') ?? 0;
//        $carPriceHigh = (int)$chat->storage()->get('car_price_high') ?? 0;


        $this->property->set(
            $chat->id,
            empty($carProperty['carBrand']) ? '' : $carProperty['carBrand'],
            empty($carProperty['carModel']) ? '' : $carProperty['carModel'],
            empty($carProperty['carPriceLow']) ? 0 : $carProperty['carPriceLow'],
            empty($carProperty['$carPriceHigh']) ? 0 : $carProperty['$carPriceHigh'],
        );


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

        $kb = Keyboard::make()->row([
            Button::make("1/$carCount")->action('page_number')->param('id', 0),
        ])
        ->row([
            Button::make('Назад')->action('show_parse_cars')->param('car_id', 0),
            Button::make('Вперед')->action('show_parse_cars')->param('car_id', 1),
        ]);
        $messId = $chat->message(
            "
Продавец: {$car['sellername']}
Город: {$car['locationname']}
Бренд: {$car['brand']}
Модель: {$car['model']}
Поколение: {$car['generation']}
Год: {$car['year']}
Цена: {$car['price']}$
Ссылка: {$car['publicurl']} "
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
        //        $chat->storage()->forget('message_id');
        $i = 0;
        $car = Redis::hGetAll("car:{$pref['car_brand']}:$i");
        $carCount = Redis::get('car_count');


        if (empty($car)) {
            $chat->message('Машин по заданным параметрам не найдено')->send();
            return;
        }

        $kb = Keyboard::make()->row([
            Button::make("1/$carCount")->action('page_number')->param('id', 0),
        ])
            ->row([
                Button::make('Назад')->action('show_parse_cars')->param('car_id', 0)->param('brand', $pref['car_brand'])    ,
                Button::make('Вперед')->action('show_parse_cars')->param('car_id', 1)->param('brand', $pref['car_brand']),
            ]);
        $messId = $chat->message(
            "
Продавец: {$car['sellername']}
Город: {$car['locationname']}
Бренд: {$car['brand']}
Модель: {$car['model']}
Поколение: {$car['generation']}
Год: {$car['year']}
Цена: {$car['price']}$
Ссылка: {$car['publicurl']} "
        )->keyboard($kb)->send()->telegraphMessageId();

        $chat->storage()->forget('car_list_message_id');
        $chat->storage()->set('car_list_message_id', $messId);

    }




}
