<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphBot as BaseModel;

class Bot extends BaseModel
{
    const NEUTRAL_STATE = 0;
    const PRICE_STATE = 1;

}
