<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\AlphabetKb;

use DefStudio\Telegraph\Models\TelegraphChat;


class Filter
{
    private AlphabetKb $alphabetKb;

    public function __construct(AlphabetKb $alphabetKb)
    {
        $this->alphabetKb = $alphabetKb;
    }
    public function addFilter(TelegraphChat $chat): void
    {
        $mess = "*Выбырите марку машины*";


        $chat->message($mess)->keyboard(
            $this->alphabetKb->getInlineKb()
        )->send();
    }
}
