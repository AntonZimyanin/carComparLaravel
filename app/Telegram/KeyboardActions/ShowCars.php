<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\FSM\StateManager;
use App\Telegram\Keyboards\Pagination\PaginationKb;
use App\Telegram\Keyboards\Builder\KeyboardBuilder;
use App\Telegram\FSM\CarFSM;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ShowCars
{
    public function __construct(
        protected SetCarModel $setCarModel,
        protected PaginationKb $paginationKb,
        protected KeyboardBuilder $kbBuilder,
        protected CarFSM $carFSM
    )
    {
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
     * @param StateManager $state
     * @throws StorageException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function showCars(TelegraphChat $chat, Collection $data, StateManager $state): void
    {
        $firstLetter = $data->get('letter') ?? $state->getData($this->carFSM->firstLetter);

        $direct = $data->get('direct') ?? 'forward';

        if ($direct === 'back' || $firstLetter) {

            $state->setData($this->carFSM->firstLetter, $firstLetter);

            $brands = $this->getAllCarBrands();
            $brandsBeginningWithLetter = array_filter($brands, function ($brand) use ($firstLetter) {
                return $brand['name'][0] === $firstLetter;
            });

            $mess = empty($brandsBeginningWithLetter) ? "Бренда с такой буквой нет" : "Машины начинаются на букву *$firstLetter*";

            $buttons = $this->getButtons($brandsBeginningWithLetter);

            $this->kbBuilder->set($buttons, 2);
            $kb = $this->kbBuilder->build();

            $lastMessId = $chat->storage()->get('message_id');
            $kb = $this->paginationKb->addPaginationToKb($kb, 'show_cars', 'car_model');

            $chat->edit($lastMessId)->message($mess)->keyboard(
                $kb
            )->send();
            return;
        }

        $this->setCarModel->handle($chat, $data, $state);



    }
}
