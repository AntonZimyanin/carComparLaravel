<?php

namespace App\Telegram\FSM;

use DefStudio\Telegraph\Contracts\StorageDriver;

class State extends Meta
{
    public bool $state;
    public mixed $value;
    public string $name;

    public function __construct(string $name) 
    {
        $this->name = $name;
    }

}
