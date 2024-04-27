<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_id')
                ->constrained('users', 'telegram_id')
                ->cascadeOnDelete();
            $table->string('car_brand');
            $table->string('car_model');
            $table->integer('car_price_low');
            $table->integer('car_price_high');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_preferences');
    }
};
