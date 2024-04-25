<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Builder\KeyboardBuilder;
use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class PriceKb extends BaseKb
{
    private PaginationKb $paginationKb;
    private KeyboardBuilder $kbBuilder;


    public function __construct(PaginationKb $paginationKb, KeyboardBuilder $kbBuilder)
    {
        $this->paginationKb = $paginationKb;
        $this->kbBuilder = $kbBuilder;
    }
    /**
     * Create an array of buttons for each price_val of the alphabet.
     *
     * @return array
     */
    private function getButtons(): array
    {
        $priceArr = range(500, 1e4, 500);
        $buttons = [];

        foreach ($priceArr as $price_val) {
            $buttons[] = Button::make('до ' . $price_val)
                ->action('set_car_price')
                ->param('car_price', $price_val);
        }

        return $buttons;
    }

    public function getInlineKb(): Keyboard
    {
        $buttons = $this->getButtons();
        $this->kbBuilder->set($buttons, 3);
        return $this->kbBuilder->build();
    }
}

