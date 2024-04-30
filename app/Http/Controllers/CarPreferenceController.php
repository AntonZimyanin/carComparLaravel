<?php

namespace App\Http\Controllers;

use App\Models\CarPreference;
use App\Telegram\Enum\AvByCarProperty;
use Illuminate\Http\Request;

class CarPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($telegramId)
    {
        return CarPreference::where('telegram_id', $telegramId)->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(AvByCarProperty $property)
    {
        $preferences = CarPreference::create([
            'telegram_id' => $property->telegramId,
            'car_brand' => $property->car_brand,
            'car_model' => $property->car_model_id,
            'car_price_low' => $property->car_price_low,
            'car_price_high' => $property->car_price_high,
        ]);

        $preferences->save();

        return $preferences;
    }

    public function getUserByTelegramId(string $telegramId) : CarPreference|null
    {
        return CarPreference::where('telegram_id', $telegramId)->first();
    }

    /**
     * Store a newly created resource in storage.
     * @param AvByCarProperty $property
     */
    public function store()
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(CarPreference $carPreference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CarPreference $carPreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CarPreference $carPreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarPreference $carPreference)
    {
        //
    }
}
