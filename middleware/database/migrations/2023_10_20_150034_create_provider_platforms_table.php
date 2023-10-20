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
        Schema::create('provider_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('account_id');
            $table->string('description');
            $table->string('access_key');
            $table->string('base_url');
            $table->string('icon_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_platforms');
    }
};
