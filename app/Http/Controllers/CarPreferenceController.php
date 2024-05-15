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
    public function index(int $chatId)
    {
        return CarPreference::where('chat_id', $chatId)->get();
    }

    public function get(int $chatId, int $id): CarPreference
    {
        return CarPreference::where('chat_id', '=', $chatId)
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(AvByCarProperty $property)
    {
        $preferences = CarPreference::create([
            'chat_id' => $property->chatId,
            'car_brand' => $property->carBrand,
            'car_model' => $property->carModelName,
            'car_price_low' => $property->carPriceLow,
            'car_price_high' => $property->carPriceHigh,
        ]);

        $preferences->save();

        return $preferences;
    }

    public function copy(int $chatId, int $id)
    {
        $preference = CarPreference::where('chat_id', $chatId)
            ->where('id', $id)
            ->first();

        $newPreference = $preference->replicate();
        $newPreference->save();

        return $newPreference;
    }

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
    public function destroy(int $chatId, int $id)
    {
        return CarPreference::where('chat_id', '=', $chatId)
            ->where('id', '=', $id)
            ->delete();
    }
}
