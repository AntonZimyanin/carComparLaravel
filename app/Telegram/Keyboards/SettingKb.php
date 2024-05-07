<?php

namespace App\Telegram\Keyboards;

use App\Http\Controllers\CarPreferenceController;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class SettingKb
{
    private CarPreferenceController $carPrefController;
    public function __construct(CarPreferenceController $carPrefController)
    {
        $this->carPrefController = $carPrefController;
    }
    public function getSettings(int $chatId = null): Keyboard
    {
        $kb = Keyboard::make()
            ->row([
                Button::make('Добавить фильтр➕')->action('add_filter')
            ]);


        $pref = $this->carPrefController->index($chatId);
        if (!empty($pref)) {
            $i = 1;
            foreach ($pref as $p) {
                if ($p['car_brand'] && $p['car_model']) {
                    $kb->row([
                        Button::make($p['car_brand'] . ' ' . $p['car_model'])->action('filter_page'),
                    ]);
                }
                else {
                    $kb->row([
                        Button::make('Фильтр ' . $i)->action('filter_page'),
                    ]);
                }
                $kb->row([
                    Button::make('⚙️')->action('change_filter')->param('pref_id', $p['id']),
                    Button::make('©️')->action('copy_filter')->param('pref_id', $p['id']),
                    Button::make('❌')->action('delete_filter')->param('pref_id', $p['id']),
                ]);
                $i++;
            }
        }

        return $kb;
    }
}
