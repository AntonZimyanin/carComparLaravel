<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class PriceKb extends BaseKb
{
    private PaginationKb $paginationKb;

    public function __construct(PaginationKb $paginationKb)
    {
        $this->paginationKb = $paginationKb;
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

    /**
     * Create a keyboard layout with the buttons arranged in rows.
     *
     * @return Keyboard
     */

    private function buildKbWithoutPagination()
    {
        $kb = Keyboard::make();
        $buttons = $this->getButtons();
        $len = count($buttons);

        for ($i = 0; $i < $len; $i += 3) {
            $step = min(3, $len - $i);
            $kb->row(array_slice($buttons, $i, $step));
        }

        return $kb;

    }
    public function getInlineKb(): Keyboard
    {
        $kb = $this->buildKbWithoutPagination();

        return $this->paginationKb->addPaginationToKb($kb, 'set_car_model');
    }
}

