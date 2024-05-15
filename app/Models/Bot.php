<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphBot as BaseModel;

class Bot extends BaseModel
{
    public const NEUTRAL_STATE = 0;
    public const PRICE_STATE = 1;

}
