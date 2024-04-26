<?php

namespace App\Telegram\Keyboards\Pagination;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class PaginationKb
{
    private int $countItems;
    private array $itemsArr = ['add_filter', 'show_cars', 'set_car_brand', 'set_car_model', "set_car_price"];
    const BACK_TO_SETTINGS = 'Вернуться к настройкам';
    const ADD_FILTER = 'Добавить фильтр';

    public function __construct()
    {
        $this->countItems = count($this->itemsArr);
    }

    private function addButtonRow(Keyboard $kb, string $prevAction, string $nextAction): void
    {
        $kb->row([
            Button::make('Назад')->action($prevAction),
            Button::make('Вперед')->action($nextAction),
        ]);
    }

    public function addPaginationToKb(Keyboard $kb, string $currentPageName): Keyboard
    {
        $page = array_search($currentPageName, $this->itemsArr);

        if ($page === false) {
            throw new Exception("Page not found: $currentPageName");
        }

        if ($page == 0) {
            $this->addButtonRow($kb, 'setting', $this->itemsArr[$page + 1]);
        } elseif ($page > 0 && $page < $this->countItems - 1) {
            $this->addButtonRow($kb, $this->itemsArr[$page - 1], $this->itemsArr[$page + 1]);
        } elseif ($page == $this->countItems - 1) {
            $kb->row([
                Button::make(self::BACK_TO_SETTINGS)->action($this->itemsArr[$page - 1]),
            ])
                ->row([
                    Button::make(self::ADD_FILTER)->action('add_filter'),
                ]);
        }

        return $kb;
    }
}

