<?php

namespace App\Telegram\KeyboardActions;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\Parser\AvBy\AvByParser;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

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
        $carPriceLow = 0;
        $carPriceHigh = $chat->storage()->get('car_price_high');

        $this->property->set(
            $chat->chat_id,
            $carBrand,
            $carModelId,
            $carPriceLow,
            $carPriceHigh,
        );

        $lastMessId = $chat->storage()->get('message_id');

        $this->parser->set(
            $this->property,
        );
        $chat->message("Поиск начат...")->send();

        $this->parser->parse($chat);

        // $chat->deleteMessage($lastMessId)->send();
        $chat->storage()->forget('message_id');


        // $this->carPrefController->create($property);
    }

}
