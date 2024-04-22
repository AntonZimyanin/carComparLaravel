<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class CarModelKb extends BaseKb
{

    private function getCarModels()
    {
        $path = base_path('brand-items-id-name-slug.json');
        $json = file_get_contents($path);
        return json_decode($json, true);
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
                $car_brand = $carModels[$j]['slug'];
                $temp[] = Button::make($car_brand)
                    ->action('car_model')
                    ->param('car_model', $car_brand);
            }
            $buttons[] = $temp;
        }
        return $buttons;
    }

    public function getInlineKb(int $page = 0): Keyboard
    {
        $buttons = $this->getButtons();
        $kb = Keyboard::make()
            ->row($buttons[0])
            ->row([
                $buttons[0][1]
            ]);
        return $kb;
    }
}
