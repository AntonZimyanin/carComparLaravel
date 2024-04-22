<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarModelKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;

use Illuminate\Support\Collection;


class CarModel
{

    private CarModelKb $carModel;

    public function __construct(CarModelKb $carModelKb)
    {
        $this->carModel = $carModelKb;
    }


    /**
     * @throws StorageException
     */
    public function setCarModel(TelegraphChat $chat, Collection $data): void
    {
        $car_model_name = $data->get("car_model_name");
        
        $chat->storage()->set('car_model_name', $car_model_name);
        $mess = "$car_model_name*Выбырите цену*";

        $kb = Keyboard::make()
            ->row([
                Button::make('100$')->action('set_car_price')->param('car_price', '100$'),
            ]);
        $chat->message($mess)->keyboard($kb)->send();
    }
}
