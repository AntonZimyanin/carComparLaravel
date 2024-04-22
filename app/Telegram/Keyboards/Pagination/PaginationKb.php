<?php

namespace App\Telegram\Keyboards\Pagination;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class Pagination
{
    private int $countItems;
    private int $itemsPerPage;

    public function __construct(array $items, int $itemsPerPage = 5)
    {
        $this->countItems = count($items);
        $this->itemsPerPage = $itemsPerPage;
    }

    public function addPaginationToKb(Keyboard $kb, int $page = 0): Keyboard
    {

        $start = $page * $this->itemsPerPage;
        $end = min($start + $this->itemsPerPage, $this->countItems);


        if ($page > 0) {
            $kb->row([
                Button::make('Previous')->action('change_page')->param('page', $page - 1),
            ]);
        }

        if ($end < $this->countItems) {
            $kb->row([
                Button::make('Next')->action('change_page')->param('page', $page + 1),
            ]);
        }

        return $kb;
    }

}
