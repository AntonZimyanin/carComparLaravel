<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Builder\Trait\KbWithPagination;
use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Keyboards\Builder\KeyboardBuilder;

use DefStudio\Telegraph\Keyboard\Button;

class AlphabetKb extends BaseKb
{
    use KbWithPagination;
    public function __construct(
        protected PaginationKb $paginationKb,
        protected KeyboardBuilder $kbBuilder
    )
    {
    }
    /**
     * Create an array of buttons for each letter of the alphabet.
     *
     * @return array
     */
    private function getButtons(): array
    {
        $alphabet = range('A', 'Z');
        $buttons = [];

        foreach ($alphabet as $letter) {
            $buttons[] = Button::make($letter)
                ->action('show_cars')
                ->param('letter', $letter);
        }

        return $buttons;
    }
}
