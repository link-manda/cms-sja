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
        Schema::create('calculator_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculator_option_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('type')->nullable(); // 2d | 3d | proses
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculator_images');
    }
};
