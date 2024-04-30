<?php

namespace App\Telegram\KeyboardActions;

use App\Http\Controllers\CarPreferenceController;
use App\Telegram\Enum\AvByCarProperty;
use App\Telegram\Parser\AvBy\AvByParser;
use DefStudio\Telegraph\Exceptions\StorageException;
use DefStudio\Telegraph\Models\TelegraphChat;

class Search
{
    private CarPreferenceController $carPrefController;

    private AvByParser $parser;
    public function __construct(
        AvByParser $parser,
        CarPreferenceController $carPrefController,
    ) {
        $this->carPrefController = $carPrefController;
        $this->parser = $parser;
    }
    /**
     * @throws StorageException
     */
    public function search(TelegraphChat $chat, AvByCarProperty $property): void
    {
        $lastMessId = $chat->storage()->get('message_id');
        $this->parser->set(
            $property,
        );
        $chat->message("Поиск начат...")->send();

        $this->parser->parse($chat);

        // $chat->deleteMessage($lastMessId)->send();
        $chat->storage()->forget('message_id');


        // $this->carPrefController->create($property);
    }

}
