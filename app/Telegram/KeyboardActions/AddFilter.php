<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\AlphabetKb;
use App\Telegram\FSM\CarFSM;
use App\Telegram\FSM\StateManager;


use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class AddFilter
{
    public function __construct(
        protected AlphabetKb $alphabetKb,
        protected CarFSM $carFSM
    )
    {}
    /**
     * @throws StorageException
     */
    public function handle(TelegraphChat $chat, StateManager $state): void
    {
        $mess = "Выбырите *первую букву* марки машины";
        $lastMessId = $chat->storage()->get('message_id');
        $kb = $this->alphabetKb->getKbWithPagination('add_filter', 'show_cars', 3);

        $state->setState($this->carFSM->firstLetter);
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
