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
        Schema::create('platform_connection', function (Blueprint $table) {
            $table->id();
            $table->integer('consumer_platform_id')->unsigned();
            $table->integer('provider_platform_id')->unsigned();
            $table->string('state');
            $table->foreign('consumer_platform_id')->references('id')->on('platform');
            $table->foreign('provider_platform_id')->references('id')->on('platform');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_connection');
    }
};
