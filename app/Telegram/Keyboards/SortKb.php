<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class SortKb
{
    public function __invoke() : Keyboard
    {
        return Keyboard::make()->row([
            Button::make('Актуальные')->action('set_sort_action')->param('sort', 1),
        ])->row([
            Button::make('Дешёвые')->action('set_sort_action')->param('sort', 2)
        ])->row([
            Button::make('Дорогие')->action('set_sort_action')->param('sort', 3),
        ])->row([
            Button::make('Новые объявления')->action('set_sort_action')->param('sort', 4),
        ])->row([
            Button::make('Старые объявления')->action('set_sort_action')->param('sort', 5),
        ])->row([
            Button::make('С наименьшим пробегом')->action('set_sort_action')->param('sort', 8),
        ])->row([
            Button::make('Новые по году')->action('set_sort_action')->param('sort', 6),
        ])->row([
            Button::make('Старые по году')->action('set_sort_action')->param('sort', 7),
        ]);
    }

}
