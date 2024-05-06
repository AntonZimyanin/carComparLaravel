<?php

namespace App\Telegram\KeyboardActions;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\Parser\AvBy\AvByParser;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Redis;

class Search
{
    private CarPreferenceController $carPrefController;

    private AvByParser $parser;
    private AvByCarProperty $property;

    public function __construct(
        AvByParser $parser,
        CarPreferenceController $carPrefController,
        AvByCarProperty $property,

    ) {
        $this->carPrefController = $carPrefController;
        $this->parser = $parser;
        $this->property = $property;
    }
    /**
     * @throws StorageException
     */
    public function search(TelegraphChat $chat): void
    {

        $carModelId = $chat->storage()->get('car_model_id');

        $carBrand = $chat->storage()->get('car_brand_text');
        $carPriceLow = (int)$chat->storage()->get('car_brand_text') ?? 0;
        $carPriceHigh = (int)$chat->storage()->get('car_price_high');

        $this->property->set(
            $chat->id,
            $carBrand,
            $carModelId,
            $carPriceLow,
            $carPriceHigh,
        );

        $this->parser->set(
            $this->property,
        );

        $this->carPrefController->create($this->property);
        $chat->message("Поиск начат...")->send();

        $this->parser->parse($chat);

        $chat->storage()->forget('message_id');

        $car = Redis::hGetAll("car:0");

        $kb = Keyboard::make()
            ->row([
                Button::make('Назад')->action('show_parse_cars')->param('id', 0),
                Button::make('Впред')->action('show_parse_cars')->param('id', 1),
            ]);
        $messId = $chat->photo($car['photourl'])->message(
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
        $chat->storage()->set('message_id', $messId);


    }

}
