<?php

namespace App\Telegram\KeyboardActions\Contracts;

use App\Telegram\FSM\StateManager;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

abstract class BaseAction
{
    public function handle(
        TelegraphChat $chat,
        Collection $data = null,
        StateManager $state = null
    ) : void {

    }

}
