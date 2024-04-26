<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarModelKb;
use App\Telegram\KeyboardActions\CarModel;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class CarBrand
{
    private CarModelKb $carModelKb;

    //if data null -> setCarModel action : action with Price Set
    private CarModel $setCarModel;


    public function __construct(CarModelKb $carModelKb, CarModel $setCarModel)
    {
        $this->carModelKb = $carModelKb;
        $this->setCarModel = $setCarModel;
    }

    /**
     * @throws StorageException
     */
    public function setCarBrand(TelegraphChat $chat, Collection $data): void
    {
        $lastMessId = $chat->storage()->get('message_id');

        if ($data->get("car_brand") && $data->get("car_brand") !== '') {
            $car_brand_text = $data->get("car_brand");
            $chat->storage()->set('car_brand_text', $car_brand_text);

            $mess = "$car_brand_text*Выбырите модель машины*";

            $kb = $this->carModelKb;
            $kb->setCarBrand($car_brand_text);
            $kb = $kb->getKbWithPagination('set_car_brand');

            $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();

            return;
        }

        $this->setCarModel->setCarModel($chat, $data);
    }
}
