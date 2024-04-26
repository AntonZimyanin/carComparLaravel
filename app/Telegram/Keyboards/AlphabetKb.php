<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Keyboards\Builder\KeyboardBuilder;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class AlphabetKb extends BaseKb
{
    private PaginationKb $paginationKb;
    private KeyboardBuilder $kbBuilder;


    public function __construct(PaginationKb $paginationKb, KeyboardBuilder $kbBuilder)
    {
        $this->paginationKb = $paginationKb;
        $this->kbBuilder = $kbBuilder;
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

    /**
      * Create a keyboard layout with the buttons arranged in rows.
      *
      * @return Keyboard
     *
     */

    public function getKbWithPagination($current_state): Keyboard
    {
        $buttons = $this->getButtons();

        $this->kbBuilder->set($buttons, 3);
        $kb = $this->kbBuilder->build();

        return $this->paginationKb->addPaginationToKb($kb, $current_state);
    }
}
