<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\AlphabetKb;
use App\Telegram\FSM\CarFSM;
use App\Telegram\FSM\StateManager;


use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class Filter
{
    private AlphabetKb $alphabetKb;
    private CarFSM $carFSM;
    public function __construct(AlphabetKb $alphabetKb, CarFSM $carFSM)
    {
        $this->alphabetKb = $alphabetKb;
        $this->carFSM = $carFSM;    
    }
    /**
     * @throws StorageException
     */
    public function addFilter(TelegraphChat $chat, StateManager $state): void
    {
        $mess = "Выбырите *первую букву* марки машины";
        $lastMessId = $chat->storage()->get('message_id');
        $kb = $this->alphabetKb->getKbWithPagination('add_filter', 'show_cars', 3);
        
        $state->setState($this->carFSM->firstLettter);
        $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
    }
}
