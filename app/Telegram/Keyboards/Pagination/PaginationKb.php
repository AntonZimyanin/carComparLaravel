<?php

namespace App\Telegram\Keyboards\Pagination;

use Illuminate\Support\Facades\Redis;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PaginationKb
{
    private const BACK_TO_SETTINGS = 'Вернуться к настройкам';
    private const ADD_FILTER = 'Добавить фильтр';
    private static array $fullPath = [
            'add_filter',
            'show_cars',
            'set_car_brand',
            'set_car_model',
            'set_car_price',
    ];
    public array $path;

    public function __construct()
    {
        $this->path = [];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addPaginationToKb(Keyboard $kb, string $currPage, string $nextPage): Keyboard
    {
        if ($currPage === 'set_car_price') {
            $this->path = [];
            return $kb->row([
                Button::make(self::ADD_FILTER)->action('add_filter'),
                Button::make(self::BACK_TO_SETTINGS)->action('back_to_settings'),
            ]);
        }

        $this->path = (array)Redis::hGetAll('path');
        if (!empty($this->path[0]) && $this->path[0] === '') {
            $this->path = [];
        }

        $tmp = $this->path;
        array_pop($tmp);

        if (end($tmp) === $currPage) {
            array_pop($this->path);
            $nextPage = $currPage;
        }

        $prevPage = end($this->path);
        $prevAction = empty($this->path) ? 'back_to_settings' : $prevPage;


        $kb->row([
            Button::make('Назад')->action($prevAction)->param('direct', 'back'),
            Button::make('Вперед')->action($nextPage)->param('direct', 'forward'),
        ]);
        $this->path[] = $currPage;


        $inx = array_search($currPage, self::$fullPath);
        Redis::hSet('path', $inx, end($this->path));

        return $kb;
    }
}
