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
        $mess = "*Выбырите марку машины*";

        $messId = $chat->message($mess)->keyboard(
            $this->alphabetKb->getInlineKb()    
        )->send()->telegraphMessageId();

        $chat->storage()->set('message_id', $messId);

    }
}
