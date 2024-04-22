<?php

namespace App\Telegram\KeyboardActions;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Array_;

class ShowCars
{
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
                ->action('set_car_model')
                ->param('car_brand', $brand['slug']);
        }

        return $buttons;
    }

    /**
     * Show a selection of car brands that start with a specific letter.
     *
     * @param TelegraphChat $chat
     * @param Collection $data
     */
    public function showCars(TelegraphChat $chat, Collection $data): void
    {
        $initLetter = $data->get('letter');
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

        $chat->message($mess)->keyboard($kb)->send();
    }
}

