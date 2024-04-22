<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarBrandKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Contracts\StorageDriver;
use Illuminate\Support\Collection;


class CarBrand
{

    private CarBrandKb $carBrandKb;

    public function __construct(CarBrandKb $carBrandKb)
    {
        $this->carBrandKb = $carBrandKb;
    }

    /**
     * @throws StorageException
     */
    public function setCarBrand(TelegraphChat $chat, Collection $data): void
    {

        $car_brand_text = $data->get("car_brand");


        $mess = "$car_brand_text*Выбырите модель машины*";

        $chat->storage()->set('car_brand_text', $car_brand_text);

        $kb = Keyboard::make()
            ->row([
                Button::make('Audi 100')->action('set_car_model')->param('car_model_name', 'Audi 100'),
            ]);

        $chat->message($mess)->keyboard($kb)->send();

    }
}
