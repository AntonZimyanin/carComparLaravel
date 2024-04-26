<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Keyboards\Builder\KeyboardBuilder;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class ShowCars
{
    private PaginationKb $paginationKb;
    private KeyboardBuilder $kbBuilder;
    private CarModel $carModel;


    public function __construct(CarModel $carModel, PaginationKb $paginationKb, KeyboardBuilder $kbBuilder)
    {
        $this->paginationKb = $paginationKb;
        $this->carModel = $carModel;
        $this->kbBuilder = $kbBuilder;
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

        if ($initLetter === null || $initLetter === '') {
            $this->carModel->setCarModel($chat, $data);
            return;
        }


        $mess = "*$initLetter*";
        $brands = $this->getAllCarBrands();
        $brandsBeginningWithLetter = array_filter($brands, function ($brand) use ($initLetter) {
            return $brand['name'][0] === $initLetter;
        });

        $buttons = $this->getButtons($brandsBeginningWithLetter);

        $this->kbBuilder->set($buttons, 2);
        $kb = $this->kbBuilder->build();

        $lastMessId = $chat->storage()->get('message_id');
        $kb = $this->paginationKb->addPaginationToKb($kb, 'show_cars');

        $chat->edit($lastMessId)->message($mess)->keyboard(
            $kb
        )->send();
    }
}
