<?php

namespace App\Http\Controllers;

use App\Models\User;
class UserController extends Controller
{
    public function getUserByTelegramId(int $telegramId) : User|null
    {
        return User::where('telegram_id', $telegramId)->first();
    }

    public function create(int $telegramId) : User
    {
        $user = User::create([
            'telegram_id' => $telegramId,
        ]);
        return $user;
    }
}
