<?php

namespace App\Telegram\Commands;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\FSM\StateManager;
use App\Telegram\Parser\AvBy\AvByParser;

use App\Telegram\Traits\SetPreference;
use DefStudio\Telegraph\Models\TelegraphChat;

class StoreCommand
{
    use SetPreference;
    public function __construct(
        protected CarPreferenceController $carPrefController,
        protected AvByCarProperty $property,
        protected AvByParser $parser,
    ) {
    }

    /**
     */
    public function store(TelegraphChat $chat, StateManager $state): void
    {
        $carProperty = $state->getAllData();
        $this->setPreference($carProperty, $chat->id, $this->property);
        $preferences = $this->carPrefController->create($this->property);

        if ($preferences) {
            $chat->message('Настройки успешно сохранены✅')->send();
        } else {
            $chat->message('Ошибка сохранения настроек')->send();
        }

    }

}
