<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\SortKb;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class SetSort
{
    private SortKb $sortKb;
    public function __construct(SortKb $sortKb)
    {
        $this->sortKb = $sortKb;

    }

    /**
     * @throws StorageException
     */
    public function get(TelegraphChat $chat): void{
        $messSortId = $chat->message("Выбери способ сортировки машин")
            ->keyboard(
                ($this->sortKb)()
            )->send()->telegraphMessageId();
        $chat->storage()->set('sort_message_id', $messSortId);
    }


}
