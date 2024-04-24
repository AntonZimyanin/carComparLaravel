<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\Pagination\PaginationKb;

abstract class BaseAction { 

    private PaginationKb $paginationKb;

    public function __construct(PaginationKb $paginationKb)
    {
        $this->paginationKb = $paginationKb;
    }
}