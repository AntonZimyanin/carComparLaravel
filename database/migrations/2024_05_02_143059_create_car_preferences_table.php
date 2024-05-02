<?php

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

            $table->foreignId('chat_id')
                ->constrained('telegraph_chats')
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
