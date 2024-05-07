<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Api\AvBy\AvByApi;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class CarModelKb extends BaseKb
{
    private AvByApi $av;
    private string $carBrandSlug;
    public function __construct(AvByApi $av) {
        $this->av = $av;
    }
    public function setCarBrand(string $carBrandSlug) : void {
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

        foreach ($carModels as $carModel) {
            $buttons[] = Button::make($carModel['name'])
                ->action('set_car_model')
                ->param('car_model_name', $carModel['slug']);
        }
        return $buttons;
    }

    public function getInlineKb(): Keyboard
    {
        $buttons = $this->getButtons();
        $len = count($buttons);
        $kb = Keyboard::make();
        for ($i = 0; $i < $len; $i += 2) {
            $step = min(2, $len - $i);
            $kb->row(array_slice($buttons, $i, $step));
        }
        return $kb;

    }
}
