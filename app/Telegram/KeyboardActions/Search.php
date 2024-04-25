<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\Parser\AvBy\AvByParser;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class Search
{
    private AvByCarProperty $property;
    private AvByParser $parser;
    public function __construct(AvByCarProperty $property,
AvByParser $parser)
    {
        $this->property = $property;
        $this->parser = $parser;
    }
    /**
     * @throws StorageException
     */
    public function search(TelegraphChat $chat): void
    {
        $car_model_id = $chat->storage()->get('car_model_id');
        $car_brand = $chat->storage()->get('car_brand_text');
        $car_price_low = 0;
        $car_price_high = $chat->storage()->get('car_price_high');


        $p = $this->property->get();
        $this->parser->set(
                 $car_brand,
        $car_model_id,
        $car_price_low,
        10000000
    );
        $chat->message("Поиск начат
        {$car_brand}\n
        {$car_model_id}
        {$this->parser->url_r()}")
            ->send()    ;




        $chat->message("{$this->parser->url_r()}")->send();

        $this->parser->parse($chat);
    }

}
