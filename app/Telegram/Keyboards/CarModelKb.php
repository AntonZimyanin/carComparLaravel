<?php

namespace App\Telegram\Keyboards;

use App\Telegram\Keyboards\Builder\Trait\KbWithPagination;
use App\Telegram\Keyboards\Builder\KeyboardBuilder;
use App\Telegram\Api\AvBy\AvByApi;

use DefStudio\Telegraph\Keyboard\Button;

class CarModelKb extends BaseKb
{
    // Use the KbWithPagination trait
    use KbWithPagination;
    private AvByApi $av;
    private string $carBrandSlug;
    private KeyboardBuilder $kbBuilder;


    public function __construct(AvByApi $av, KeyboardBuilder $kbBuilder)
    {
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

    /**
     * @return array<Button>
     */
    public function getButtons(): array
    {
        $carModels = $this->getCarModels();
        $buttons = [];

        foreach ($carModels as $carModel) {
            $buttons[] = Button::make($carModel['slug'])
                ->action('set_car_model')
                ->param('car_model_id', $carModel['id'])
                ->param('car_model_name', $carModel['slug']);
            ;
        }
        return $buttons;
    }
}
