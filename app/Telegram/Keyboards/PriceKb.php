<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Builder\KeyboardBuilder;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class PriceKb extends BaseKb
{
    private KeyboardBuilder $kbBuilder;

    public function __construct(KeyboardBuilder $kbBuilder)
    {
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

    /**
     * return Keyboard with Pagination
     */
    public function getKbWithPagination($current_state): Keyboard
    {
        $buttons = $this->getButtons();
        $this->kbBuilder->set($buttons, 3);
        return  $this->kbBuilder->buildWithPagination($current_state);
    }
}
