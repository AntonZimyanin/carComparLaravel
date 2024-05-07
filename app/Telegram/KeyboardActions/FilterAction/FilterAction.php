<?php

namespace App\Telegram\KeyboardActions\FilterAction;

use App\Http\Controllers\CarPreferenceController;
use Illuminate\Support\Collection;

class FilterAction
{
    private CarPreferenceController $carPrefController;
    public function __construct(CarPreferenceController $carPrefController)
    {
        $this->carPrefController = $carPrefController;
    }

    public function del($chatId, Collection $data): void
    {
        $prefId = $data->get('pref_id');
        $this->carPrefController->destroy($chatId, $prefId);
    }
    public function copy($chatId, Collection $data): void
    {
        $prefId = $data->get('pref_id');
        $this->carPrefController->copy($chatId, $prefId);
    }

    public function edit($chatId, Collection $data): void
    {
        $prefId = $data->get('pref_id');
        $this->carPrefController->update($chatId, $prefId);
    }

}
