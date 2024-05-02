<?php

namespace App\Telegram\Keyboards\Pagination;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class PaginationKb
{
    const BACK_TO_SETTINGS = 'Вернуться к настройкам';
    const ADD_FILTER = 'Добавить фильтр';

    private static ?PaginationKb $instance = null;

    private static array $fullPath = [
'add_filter',
'show_cars',
'set_car_brand',
'set_car_model',
'set_car_price',
];
    public static ?array $path = [];

    public string $state = '';


    public function __construct()
    {
    }

    public static function getInstance(): PaginationKb
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }


    private function addButtonRow(Keyboard $kb, string $prevAction, string $nextAction): void
    {
        $kb->row([
            Button::make('Назад')->action($prevAction),
            Button::make('Вперед')->action($nextAction),
        ]);
    }


    //TODO: correct logic
    static function addPaginationToKb(Keyboard $kb, string $currPage, string $nextPage): Keyboard
    {
        $prevPage = end(self::$path);
        $prevAction = count(self::$path) == 0 ? 'back_to_settings' : end(self::$path);


        if ($prevPage === $currPage) {
            return $kb->row([
                Button::make('Назад')->action($prevAction),
                Button::make('Вперед')->action($nextPage),
            ]);
        }



        if (count(self::$path) < count(self::$fullPath) - 3) {
            $kb->row([
                Button::make('Назад')->action($prevAction),
                Button::make('Вперед')->action($nextPage),
            ]);

        }
        if ($currPage === 'set_car_price') {
            $kb->row([
                Button::make(self::ADD_FILTER)->action('add_filter'),
                Button::make(self::BACK_TO_SETTINGS)->action('back_to_settings'),
            ]);
//            self::$path = [];
        }

        self::$path[] = $currPage;


        return $kb;
    }
}

