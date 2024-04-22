<?php


namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Pagination\Pagination;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;


class CarBrandKb extends BaseKb
{

    private Pagination $pagination;
    public function __construct(Pagination $pagination) { 
        $this->pagination = $pagination;
    }
    private function getAllCarBrands(): mixed
    {
        $path = base_path('brand-items-id-name-slug.json');
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    public function getButtons(): array
    {
        $brands = $this->getAllCarBrands();
        $buttons = [];
        $len = count($brands);
        for ($i = 0; $i < $len; $i += 3) {
            $temp = [];
            $step = min($i + 3, $len);
            for ($j = $i; $j < $step; $j++) {
                $car_brand = $brands[$j]['slug'];
                $temp[] = Button::make($car_brand)
                    ->action('set_car_brand')
                    ->param('car_brand', $car_brand);
            }
            $buttons[] = $temp;
        }
        return $buttons;
    }

    public function getInlineKb(int $page = 0): Keyboard
    {
        $buttons = $this->getButtons();
        $kb = Keyboard::make();
        for ($i = 0; $i < 4; $i++) {
            $kb->row($buttons[$i]);
        }
        
        

        return $kb;
    }
}