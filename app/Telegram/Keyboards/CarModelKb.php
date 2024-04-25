<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Builder\KeyboardBuilder;
use App\Telegram\Api\AvBy\AvByApi;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class CarModelKb extends BaseKb
{
    private AvByApi $av;
    private string $carBrandSlug;
    private KeyboardBuilder $kbBuilder;


    public function __construct(AvByApi $av, KeyboardBuilder $kbBuilder) {
        $this->av = $av;
        $this->kbBuilder = $kbBuilder;
    }

    public function setCarBrand(string $carBrandSlug): void
    {
        $this->carBrandSlug = $carBrandSlug;
    }

    private function getCarModels()
    {
        return $this->av->getModels($this->carBrandSlug);

    }
    public function getButtons(): array
    {
        $carModels = $this->getCarModels();
        $buttons = [];

        foreach ($carModels as $carModel) {
            $buttons[] = Button::make($carModel['slug'])
                ->action('set_car_model')
                ->param('car_model_id', $carModel['id']);
        }
        return $buttons;
    }

    public function getInlineKb(): Keyboard
    {
        $buttons = $this->getButtons();
        $this->kbBuilder->set($buttons, 2);
        return $this->kbBuilder->build();

    }


}
