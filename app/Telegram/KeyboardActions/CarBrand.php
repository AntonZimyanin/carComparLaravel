<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarModelKb;
use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;


class CarBrand
{
    private CarModel $carModel;
    private PaginationKb $paginationKb;
    private CarModelKb $carModelKb;

    public function __construct(
        CarModelKb $carModelKb,
        PaginationKb $paginationKb,
        CarModel $carModel
    )
    {
        $this->carModelKb = $carModelKb;
        $this->paginationKb = $paginationKb;
        $this->carModel = $carModel;
    }

    /**
     * @throws StorageException
     */
    public function setCarBrand(TelegraphChat $chat, Collection $data): void
    {
        $lastMessId = $chat->storage()->get('message_id');
        $carBrandName = $data->get('car_brand');

        if (!($carBrandName) && $chat->storage()->get('car_brand_name')) {
            $this->carModel->setCarModel($chat, $data);
            return;
        }
        $chat->storage()->set('car_brand_name', $carBrandName);

        $mess = "*Выбырите модель машины*";

        $kb = $this->carModelKb;
        $kb->setCarBrand($carBrandName);
        $kb = $kb->getInlineKb();
        $kb = $this->paginationKb->addPaginationToKb($kb, 'set_car_brand', 'set_car_model');

        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
