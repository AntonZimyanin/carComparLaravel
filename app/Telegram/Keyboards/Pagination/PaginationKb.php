<?php

namespace App\Telegram\Keyboards\Pagination;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class PaginationKb
{
    private int $countItems;
    private array $itemsArr = ['add_filter', 'show_cars', 'set_car_brand', 'set_car_model', "set_car_price"];

    public function __construct()
    {
        $this->countItems = count($this->itemsArr);
    }

    public function addPaginationToKb(Keyboard $kb, string $currentPageStr): Keyboard
    {
        $page = array_search($currentPageStr, $this->itemsArr);

        if ($page == 0) {
            $kb->row([
                Button::make('Previous')->action('setting'),
                Button::make('Next')->action($this->itemsArr[$page + 1])->param('page', $page + 1),

            ]);
        }

        if ($page > 0 && $page < $this->countItems - 1) {
            $kb->row([
                Button::make('Previous')->action('change_page')->param('page', $page - 1),
                Button::make('Next')->action('car_price')->param('page', $page + 1),

            ]);
        }


        if ($page == $this->countItems - 1) {
            $kb->row([
                    Button::make('Вернуться к настройкам')->action('setting'),
            ]);
        }


        return $kb;
    }

}
