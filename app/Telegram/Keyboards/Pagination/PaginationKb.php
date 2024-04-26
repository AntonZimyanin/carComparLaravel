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

    public function addPaginationToKb(Keyboard $kb, string $currentPageName): Keyboard
    {
        $page = array_search($currentPageName, $this->itemsArr);

        if ($page == 0) {
            $kb->row([
                Button::make('Previous')->action('setting'),
                Button::make('Next')->action($this->itemsArr[$page + 1]),

            ]);
        }

        if ($page > 0 && $page < $this->countItems - 1) {
            $kb->row([
                Button::make('Previous')->action($this->itemsArr[$page - 1]),
                Button::make('Next')->action($this->itemsArr[$page + 1]),

            ]);
        }


        if ($page == $this->countItems - 1) {
            $kb->row([
                Button::make('Вернуться к настройкам')->action($this->itemsArr[$page - 1]),
            ])
            ->row([
                Button::make('Добавить фильтр')->action('add_filter'),
            ]);
        }


        return $kb;
    }

}
