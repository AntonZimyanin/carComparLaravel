<?php

namespace App\Telegram\FSM;

use DefStudio\Telegraph\Contracts\StorageDriver;


class StateManager
{
    private StorageDriver $storage;

    protected array $fillable =   [
        'carBrand',
        'carModel',
        'carPriceLow',
        'carPriceHigh',
    ];

    protected array $allFillable = [
        'firstLettter'
    ];

    public function __construct(StorageDriver $storage)
    {
        $this->storage = $storage;
        array_push($this->allFillable, ...$this->fillable);
    }

    public function setState(State $state) : void
    {
        $this->storage->set("$state->name:state", true);
    }

    public function getState(State $state) : string
    {
        return $this->storage->get("$state->name:state");
    }

    public function forgetState(State $state) : void
    {
        $this->storage->forget("$state->name:state");
    }

    public function forgetData(State $state) : void
    {
        $this->storage->forget("$state->name:data");
    }

    public function setData(State $state, mixed $value) : void
    {
        $this->storage->set("$state->name:data", $value);
    }

    public function getData(State $state) : mixed
    {
        return $this->storage->get("$state->name:data");
    }

    public function getAllData() : array
    {
        $out = [];
        foreach ($this->fillable as $stateName) {
            $out[$stateName] = $this->storage->get("$stateName:data");
        }

        return $out;
    }

    public function clear(): void
    {
        foreach ($this->allFillable as $stateName) {
            $this->storage->forget("$stateName:data");
            $this->storage->forget("$stateName:state");
        }
    }

}
