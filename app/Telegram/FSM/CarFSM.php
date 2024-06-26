<?php

namespace App\Telegram\FSM;


class Meta
{
    public function __construct($obj)
    {
        $a = get_object_vars($obj);
        foreach ($a as $key => $value) {
            $this->$key = $key . " is set";
        }
    }
}


class CarFSM extends FSM
{
    public State $firstLetter;
    public State $carBrand;
    public State $carModel;
    public State $carPriceLow;
    public State $carPriceHigh;

    public function __construct() {
        $this->firstLetter = new State('firstLetter');
        $this->carBrand = new State('carBrand');
        $this->carModel = new State('carModel');
        $this->carPriceLow = new State('carPriceLow');
        $this->carPriceHigh = new State('carPriceHigh');
    }

}
