<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\AlphabetKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;


class Filter
{
    private AlphabetKb $alphabetKb;

    public function __construct(AlphabetKb $alphabetKb)
    {
        $this->alphabetKb = $alphabetKb;
    }

    /**
     * @throws StorageException
     */
    public function addFilter(TelegraphChat $chat): void
    {
        $mess = "Выбырите *первую букву* марки машины";
        $lastMessId = $chat->storage()->get('message_id');

        $chat->edit($lastMessId)->message($mess)->keyboard( $this->alphabetKb->getInlineKb())->send();
    }
}
