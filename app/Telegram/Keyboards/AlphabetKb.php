<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class AlphabetKb extends BaseKb
{
    public function getInlineKb(): Keyboard
    {
        $keyboard = Keyboard::make();
        $alphabet = range('A', 'Z');

        foreach ($alphabet as $letter) {
            $keyboard->row([
                Button::make($letter)->action('show_cars')->param('letter', $letter),
            ]);
        }

        return $keyboard;
    }
}

class ShowCarsAction
{
    private AlphabetKb $alphabetKb;

    public function __construct(AlphabetKb $alphabetKb)
    {
        $this->alphabetKb = $alphabetKb;
    }

    public function show_cars(StorageDriver $storage): void
    {
        $letter = $storage->get['letter'];

        

    }
}
