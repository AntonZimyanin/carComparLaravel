<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarBrandKb;

use DefStudio\Telegraph\Models\TelegraphChat;


class Filter
{
    private CarBrandKb $carBrandKb;

    public function __construct(carBrandKb $carBrandKb)
    {
        $this->carBrandKb = $carBrandKb;
    }
    public function addFilter(TelegraphChat $chat): void
    {
        $mess = "*Выбырите марку машины*";

        $kb = $this->carBrandKb->getInlineKb();
        $chat->message($mess)->keyboard($kb)->send();
    }
}
