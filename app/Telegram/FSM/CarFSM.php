<?php

namespace App\Telegram\FSM;

use DefStudio\Telegraph\Contracts\StorageDriver;


class Meta {
    public function __construct($obj) {
        $a = get_object_vars($obj);
        foreach ($a as $key => $value){
            $this->$key = $key . " is set";
        }
    }
}
class State extends Meta
{
    private StorageDriver $storage;

    public string $field;

    public function __construct(StorageDriver $storage)
    {
        parent::__construct($this);
        $this->storage = $storage;
    }
    public function set($value): void
    {
        $this->field = $value;
    }

    public function get(): string
    {
        return $this->field;
    }

}

class CarFSM
{
    public State $carBrand;
    public State $carModel;
    public State $carPriceLow;
    public State $carPriceHigh;

    public function __construct()
    {
        $this->carBrand = new State();
        $this->carModel = new State();
        $this->carPriceLow = new State();
        $this->carPriceHigh = new State();
    }

}
