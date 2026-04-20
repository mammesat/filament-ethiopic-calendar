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
        Schema::create('test_settings', function (Blueprint $table) {
            $table->id();
            $table->string('display_mode')->default('amharic_combined');
            $table->string('calendar_locale')->default('am');
            $table->boolean('with_time')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_settings');
    }
};
