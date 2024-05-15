<?php

namespace App\Telegram\FSM;

use DefStudio\Telegraph\Contracts\StorageDriver;


class StateManager
{
    private StorageDriver $storage;

    public function __construct(StorageDriver $storage)
    {
        $this->storage = $storage;
    }

    public function setState(State $state) : void
    {
        $this->storage->set("$state->name:state", true);
    }

    public function getState(State $state) : string
    {
        return $this->storage->get("$state->name:state");
    }

    public function forget(State $state) : void
    {
        $this->storage->forget("$state->name:state");
        $this->storage->forget("$state->name:data");
    }

    public function setData(State $state, mixed $value) : void
    {
        $this->storage->set("$state->name:data", $value);
    }

    public function getData(State $state) : string
    {
        return $this->storage->get("$state->name:data");
    }

}
