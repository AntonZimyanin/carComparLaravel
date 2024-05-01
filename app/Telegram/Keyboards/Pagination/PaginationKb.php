<?php

namespace App\Telegram\Keyboards\Pagination;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class PaginationKb
{
    private int $countItems;
    private array $itemsArr = ['setting', 'add_filter', 'show_cars', 'set_car_brand', 'set_car_model', "set_car_price"];
    const BACK_TO_SETTINGS = 'Вернуться к настройкам';
    const ADD_FILTER = 'Добавить фильтр';

    private array $fullPath;
    private array $path;


    public function __construct()
    {
        $this->path = [];

        $this->fullPath = [
            'setting' => 'add_filter',
            'add_filter' => 'show_cars',
            'show_cars' => 'set_car_brand',
            'set_car_brand' => 'set_car_model',
            'set_car_model' => 'set_car_price',
        ];
    }

    private function addButtonRow(Keyboard $kb, string $prevAction, string $nextAction): void
    {
        $kb->row([
            Button::make('Назад')->action($prevAction),
            Button::make('Вперед')->action($nextAction),
        ]);
    }

    public function addPaginationToKb(Keyboard $kb, string $currPage, string $nextPage): Keyboard
    {
        $i = 0;
        $sequence = [0 => [
            'setting' => 'add_filter',
            'add_filter' => 'show_cars',
            'show_cars' => 'set_car_brand',
            'set_car_brand' => 'set_car_model',
            'set_car_model' => false
        ]

        ];
        $prevPage = end($this->path);
        if (empty($this->path)) { 
            $kb->row([
                Button::make('Назад')->action('back_to_settings'),
                Button::make('Вперед')->action($nextPage),
            ]);
        }

        if ($prevPage == $currPage) {
            $kb->row([
                Button::make('Назад')->action($this->path[count($this->path) - 2]),

                Button::make('Вперед')->action($nextPage),
            ]);
        }
        else { 
            // $kb->row([
            //     Button::make('Назад')->action($currPage),
            //     Button::make('Вперед')->action($nextPage),
            // ]);
        }
        


        
        $this->path[$currPage] = $nextPage;

        return $kb;

        // if ($page == 0) {
        //     $this->addButtonRow($kb, 'back_to_settings', $this->itemsArr[$page + 1]);
        // } elseif ($page > 0 && $page < $this->countItems - 1) {
        //     $this->addButtonRow($kb, $this->itemsArr[$page - 1], $this->itemsArr[$page + 1]);
        // } elseif ($page == $this->countItems - 1) {
        //     $kb->row([
        //         Button::make(self::BACK_TO_SETTINGS)->action($this->itemsArr[$page - 1]),
        //     ])
        //         ->row([
        //             Button::make(self::ADD_FILTER)->action('add_filter'),
        //         ]);
        // }

    }
}


class PagintionKeyboard
{
    private array $fullPath;
    private array $path;


    public function __construct()
    {
        $path = [];

        $fullPath = [
            'setting' => 'add_filter',
            'add_filter' => 'show_cars',
            'show_cars' => 'set_car_brand',
            'set_car_brand' => 'set_car_model',
            'set_car_model' => 'set_car_price',
        ];
    }


    public function addPaginationToKb(Keyboard $kb, string $currPage, string $nextPage): Keyboard
    {
        $prevPage = end($this->path);
        if ($prevPage == $currPage) {
            $kb->row([
                Button::make('Назад')->action($currPage),
                Button::make('Вперед')->action($nextPage),
            ]);
        }
        else { 
            // $kb->row([
            //     Button::make('Назад')->action($currPage),
            //     Button::make('Вперед')->action($nextPage),
            // ]);
        }

        $path[$currPage] = $nextPage;

        return $kb;
    }
}
