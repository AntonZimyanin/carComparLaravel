<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarModelKb;
use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;


class CarBrand
{

    private PaginationKb $paginationKb;
    private CarModelKb $carModelKb;

    public function __construct(CarModelKb $carModel, PaginationKb $paginationKb)
    {
        $this->carModelKb = $carModel;
        $this->paginationKb = $paginationKb;
    }

    /**
     * @throws StorageException
     */
    public function setCarBrand(TelegraphChat $chat, Collection $data): void
    {
        $lastMessId = $chat->storage()->get('message_id');
        $car_brand_text = $data->get("car_brand");
        $chat->storage()->set('car_brand_text', $car_brand_text);

        $mess = "$car_brand_text*Выбырите модель машины*";

        $kb = $this->carModelKb;
        $kb->setCarBrand($car_brand_text);
        $kb = $kb->getInlineKb();
        $kb = $this->paginationKb->addPaginationToKb($kb, 'set_car_brand');

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
