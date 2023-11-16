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
        Schema::create('platform_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_platform_id')->references('id')->on('consumer_platforms');
            $table->foreignId('provider_platform_id')->references('id')->on('provider_platforms');
            $table->string('state')->default('disabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_connections');
    }
};
