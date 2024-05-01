<?php

namespace App\Telegram\Keyboards\Builder\Trait;

use DefStudio\Telegraph\Keyboard\Keyboard;

trait KbWithPagination
{
    /**
     * Create a keyboard layout with the buttons arranged in rows.
     *
     * @param string $current_state
     * @param int $buttonsPerRow
     * @return Keyboard
     */
    public function getKbWithPagination(string $currPage, string $nextPage, int $buttonsPerRow): Keyboard
    {
        $buttons = $this->getButtons();
        $this->kbBuilder->set($buttons, $buttonsPerRow);
        return  $this->kbBuilder->buildWithPagination($currPage, $nextPage);
    }

}
