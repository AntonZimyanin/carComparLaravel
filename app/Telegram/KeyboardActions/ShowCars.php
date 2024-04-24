<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\CarModelKb;
use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class ShowCars
{
    private PaginationKb $paginationKb;
    private CarPrice $carPrice;

    public function __construct(CarPrice $carPrice, PaginationKb $paginationKb)
    {
        $this->paginationKb = $paginationKb;
        $this->carPrice = $carPrice;
    }

    /**
     * Load car brand data from a JSON file.
     *
     * @return mixed
     */

    private function getAllCarBrands(): mixed
    {
        $path = base_path('brand-items-id-name-slug.json');
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    private function getButtons(array $brands): array
    {
        $buttons = [];

        foreach ($brands as $brand) {
            $buttons[] = Button::make($brand['slug'])
                ->action('set_car_brand')
                ->param('car_brand', $brand['slug']);
        }

        return $buttons;
    }

    /**
     * Show a selection of car brands that start with a specific letter.
     *
     * @param TelegraphChat $chat
     * @param Collection $data
     * @throws StorageException
     */
    public function showCars(TelegraphChat $chat, Collection $data): void
    {
        $initLetter = $data->get('letter');

        if ($initLetter === null) {
            $this->carPrice->setCarPrice($chat, $data);
            return;
        }


        $mess = "*$initLetter*";
        $brands = $this->getAllCarBrands();
        $brandsBeginningWithLetter = array_filter($brands, function ($brand) use ($initLetter) {
            return $brand['name'][0] === $initLetter;
        });

        $kb = Keyboard::make();
        $buttons = $this->getButtons($brandsBeginningWithLetter);
        $len = count($buttons);
        for ($i = 0; $i < $len; $i += 3) {
            $step = min(3, $len - $i);
            $kb->row(array_slice($buttons, $i, $step));
        }
        $lastMessId = $chat->storage()->get('message_id');
        $kb = $this->paginationKb->addPaginationToKb($kb, 'show_cars');

        $chat->edit($lastMessId)->message($mess)->keyboard(
            $kb
        )->send();
    }
}
