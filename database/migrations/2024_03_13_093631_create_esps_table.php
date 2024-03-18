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
        Schema::create('esps', function (Blueprint $table) {
            $table->id();
            $table->decimal('lang', 10, 7); // Latitude
            $table->decimal('lat', 10, 7); // Longitude
            $table->unsignedTinyInteger('battery_percentage'); // Battery percentage (0-100)
            $table->string('name'); // Name of the ESP
            $table->boolean('is_online'); // Online status (true/false)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esps');
    }
};
