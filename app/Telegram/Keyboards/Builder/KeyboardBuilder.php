<?php

namespace App\Telegram\Keyboards\Builder;

use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Keyboard\Keyboard;

class KeyboardBuilder
{
    private array $buttons;
    private int $buttonsPerRow;
    private PaginationKb $paginationKb;

    public function __construct(PaginationKb $paginationKb)
    {
        $this->paginationKb = $paginationKb;
    }

    public function set(array $buttons, int $buttonsPerRow = 2): void
    {
        $this->buttons = $buttons;
        $this->buttonsPerRow = $buttonsPerRow;
    }

    public function build(): Keyboard
    {
        $kb = Keyboard::make();
        $len = count($this->buttons);
        for ($i = 0; $i < $len; $i += $this->buttonsPerRow) {
            $step = min($this->buttonsPerRow, $len - $i);
            $kb->row(array_slice($this->buttons, $i, $step));
        }
        return $kb;
    }


    public function buildWithPagination(string $current_state): Keyboard
    {
        $kb = Keyboard::make();
        $len = count($this->buttons);
        for ($i = 0; $i < $len; $i += $this->buttonsPerRow) {
            $step = min($this->buttonsPerRow, $len - $i);
            $kb->row(array_slice($this->buttons, $i, $step));
        }
        return $this->paginationKb->addPaginationToKb($kb, $current_state);
    }
}
