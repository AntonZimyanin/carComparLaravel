<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class AlphabetKb extends BaseKb
{
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
     */
    public function getInlineKb(): Keyboard
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
}

