<?php

namespace App\Telegram\Keyboards\Pagination;

use Illuminate\Support\Facades\Redis;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PaginationKb
{
    public const BACK_TO_SETTINGS = 'Вернуться к настройкам';
    public const ADD_FILTER = 'Добавить фильтр';
    private static array $fullPath = [
            'add_filter',
            'show_cars',
            'set_car_brand',
            'set_car_model',
            'set_car_price',
    ];
    public static array $path;

    public function __construct()
    {
        self::$path = [];
    }
    private function addButtonRow(Keyboard $kb, string $prevAction, string $nextAction): void
    {
        $kb->row([
            Button::make('Назад')->action($prevAction),
            Button::make('Вперед')->action($nextAction),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPath(): array
    {
        for ($i = 0; $i < count(self::$fullPath); $i++) {
            if (Redis::get("path:$i") !== null) {
                self::$path[] = Redis::get("path:$i");
            }
        }

        return self::$path;
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addPaginationToKb(Keyboard $kb, string $currPage, string $nextPage): Keyboard
    {
        if ($currPage === 'set_car_price') {
            self::$path = [];
            return $kb->row([
                Button::make(self::ADD_FILTER)->action('add_filter'),
                Button::make(self::BACK_TO_SETTINGS)->action('back_to_settings'),
            ]);
        }

        self::$path = (array)Redis::hGetAll('path');
        if (!empty(self::$path[0]) && self::$path[0] === '') {
            self::$path = [];
        }

        $prevPage = end(self::$path);
        $prevAction = empty(self::$path) ? 'back_to_settings' : $prevPage;

        if ($prevPage === $currPage) {
            array_pop(self::$path);
        }
        $kb->row([
            Button::make('Назад')->action($prevAction),
            Button::make('Вперед')->action($nextPage),
        ]);
        self::$path[] = $currPage;


        $inx = array_search($currPage, self::$fullPath);
        Redis::hSet('path', $inx, end(self::$path));

        return $kb;
    }
}
