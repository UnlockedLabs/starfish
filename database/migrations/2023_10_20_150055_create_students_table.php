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
<<<<<<< HEAD:database/migrations/2023_10_20_150055_create_students_table.php
        Schema::create('students', function (Blueprint $table) {
            $table->id()->uuid();
=======
        Schema::create('platform_connection', function (Blueprint $table) {
            $table->id();
            $table->integer('consumer_platform_id')->unsigned();
            $table->integer('provider_platform_id')->unsigned();
            $table->string('state');
            $table->foreign('consumer_platform_id')->references('id')->on('platform');
            $table->foreign('provider_platform_id')->references('id')->on('platform');
>>>>>>> 9f9583a (fix: continued work):middleware/database/migrations/2023_09_26_175535_create_platform_connection.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
