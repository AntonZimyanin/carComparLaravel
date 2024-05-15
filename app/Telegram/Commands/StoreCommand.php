<?php

namespace App\Telegram\Commands;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\Parser\AvBy\AvByParser;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class StoreCommand
{
    private CarPreferenceController $carPrefController;
    private AvByCarProperty $property;
    private AvByParser $parser;
    public function __construct(
        CarPreferenceController $carPrefController,
        AvByCarProperty $property,
        AvByParser $parser,
    ) {
        $this->carPrefController = $carPrefController;
        $this->parser = $parser;
        $this->property = $property;
    }

    /**
     * @throws StorageException
     */
    public function store(TelegraphChat $chat): void
    {
        $carModelName = $chat->storage()->get("car_model_name") ?? '';
        $carBrand = $chat->storage()->get('car_brand_name') ?? '';
        $carPriceLow = (int)$chat->storage()->get('car_price_low') ?? 0;
        $carPriceHigh = (int)$chat->storage()->get('car_price_high') ?? 0;

        $this->property->set(
            $chat->id,
            $carBrand,
            $carModelName,
            $carPriceLow,
            $carPriceHigh,
        );

        $filter = $this->carPrefController->create($this->property);


        // $chat->storage()->forget('car_brand_name');
        // $chat->storage()->forget('car_model_name');
        // $chat->storage()->forget('car_price_low');
        // $chat->storage()->forget('car_price_high');

        if ($filter) {
            $chat->message('Настройки успешно сохранены✅')->send();
        } else {
            $chat->message('Ошибка сохранения настроек')->send();
        }

    }

}
