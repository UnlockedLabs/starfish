<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/*

Type: string ('Unlockedv1', etc.)

Name: string

ApiKey: string

BaseUrl: string
*/

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consumer_platform', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('api_key');
            $table->string('base_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumer_platform');
    }
};
