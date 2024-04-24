<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Api\AvBy\AvByApi;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class CarModelKb extends BaseKb
{
    private AvByApi $av;
    private $carBrandSlug;
    
    public function __construct(AvByApi $av) { 
        $this->av = $av;
    }

    public function setCarBrand(string $carBrandSlug) { 
        $this->carBrandSlug = $carBrandSlug;
    }

    private function getCarModels()
    {
        return $this->av->getModels($this->carBrandSlug);

    }
    public function getButtons(): array
    {
        $carModels = $this->getCarModels();
        $buttons = [];
        $len = count($carModels);
        for ($i = 0; $i < $len; $i += 4) {
            $temp = [];
            $step = min($i + 4, $len);
            for ($j = $i; $j < $step; $j++) {
                $carModel = $carModels[$j]['slug'];
                $temp[] = Button::make($carModel)
                    ->action('set_car_model')
                    ->param('car_model', $carModel);
            }
            $buttons[] = $temp;
        }
        return $buttons;
    }

    public function getInlineKb(): Keyboard
    {
        $buttons = $this->getButtons();
        return Keyboard::make()->buttons(...$buttons);
           
    }
}
