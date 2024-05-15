<?php

namespace App\Telegram\KeyboardActions;

use App\Telegram\FSM\CarFSM;
use App\Telegram\FSM\StateManager;
use App\Telegram\Keyboards\CarModelKb;
use App\Telegram\Keyboards\Pagination\PaginationKb;

use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;

class SetCarBrand
{
    private SetCarModel $setCarModel;
    private PaginationKb $paginationKb;
    private CarModelKb $carModelKb;
    private CarFSM $carFSM;

    public function __construct(
        CarModelKb   $carModelKb,
        PaginationKb $paginationKb,
        SetCarModel  $setCarModel,
        CarFSM       $carFSM
    ) {
        $this->carModelKb = $carModelKb;
        $this->paginationKb = $paginationKb;
        $this->setCarModel = $setCarModel;
        $this->carFSM = $carFSM;

    }

    /**
     * @throws StorageException
     */
    public function handle(TelegraphChat $chat, Collection $data, StateManager $state): void
    {
        $lastMessId = $chat->storage()->get('message_id');
        $carBrandName = $data->get('car_brand') ?? $state->getData($this->carFSM->carBrand);



        if (($data->get('direct') === 'back' and $state->getData($this->carFSM->firstLettter)) || $carBrandName) {
            $state->setData($this->carFSM->carBrand, $carBrandName);

            $mess = "*Выбырите модель машины*";

            $kb = $this->carModelKb;
            $kb->setCarBrand($carBrandName);
            $kb = $kb->getInlineKb();
            $kb = $this->paginationKb->addPaginationToKb($kb, 'set_car_brand', 'set_car_model');

            $chat->edit($lastMessId)->message($mess)->keyboard($kb)->send();
            return;
        }

        $this->setCarModel->handle($chat, $data, $state);

    }
}
