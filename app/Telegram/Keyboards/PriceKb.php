<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Builder\KeyboardBuilder;

use App\Telegram\Keyboards\Builder\Trait\KbWithPagination;
use DefStudio\Telegraph\Keyboard\Button;

class PriceKb extends BaseKb
{
    use KbWithPagination;

    public function __construct(protected KeyboardBuilder $kbBuilder)
    {
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
                ->param('car_price_high', $price_val);
        }

        return $buttons;
    }
}
