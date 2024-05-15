<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Keyboards\Builder\KeyboardBuilder;
use App\Telegram\FSM\CarFSM;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class ShowCars
{
    private PaginationKb $paginationKb;
    private KeyboardBuilder $kbBuilder;
    private CarModel $carModel;
    private CarFSM $carFSM;


    public function __construct(CarModel $carModel, PaginationKb $paginationKb, KeyboardBuilder $kbBuilder, CarFSM $carFSM)
    {
        $this->paginationKb = $paginationKb;
        $this->carModel = $carModel;
        $this->kbBuilder = $kbBuilder;
        $this->carFSM = $carFSM;    
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
            $buttons[] = Button::make($brand['name'])
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
        // 0 and 1 = 0
        // 0 and 0 = 0
        // 1 and 1 = 1

        if ( !($initLetter) && !($chat->storage->get('init_letter'))) { 
            $this->carModel->setCarModel($chat, $data);
            return;
        }

        $chat->storage()->set('init_letter', $initLetter);
        $brands = $this->getAllCarBrands();
        $brandsBeginningWithLetter = array_filter($brands, function ($brand) use ($initLetter) {
            return $brand['name'][0] === $initLetter;
        });

        $mess = empty($brandsBeginningWithLetter) ? "Бренда с такой буквой нет" : "Машины начинаются на букву *$initLetter*";

        $buttons = $this->getButtons($brandsBeginningWithLetter);

        $this->kbBuilder->set($buttons, 2);
        $kb = $this->kbBuilder->build();

        $lastMessId = $chat->storage()->get('message_id');
        $kb = $this->paginationKb->addPaginationToKb($kb, 'show_cars', 'car_model');

        $chat->edit($lastMessId)->message($mess)->keyboard(
            $kb
        )->send();
    }
}
