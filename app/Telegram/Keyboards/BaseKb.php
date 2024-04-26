<?php

namespace App\Telegram\Keyboards;

use DefStudio\Telegraph\Keyboard\Button;

abstract class BaseKb
{
    /**
     * @return array<Button>
     */
    private function getButtons(): array
    {
        return [];
    }
}
