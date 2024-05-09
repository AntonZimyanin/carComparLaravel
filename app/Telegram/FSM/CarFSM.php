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

class CarFSM
{
    public State $carBrand;
    public State $carModel;
    public State $carPriceLow;
    public State $carPriceHigh;

    public function __construct(
        State $carBrand,
        State $carModel,
        State $carPriceLow,
        State $carPriceHigh
    )
    {
        $this->carBrand = $carBrand;
        $this->carModel = $carModel;
        $this->carPriceLow = $carPriceLow;
        $this->carPriceHigh = $carPriceHigh;
    }

}
