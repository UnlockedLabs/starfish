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
        Schema::create('provider_contents', function (Blueprint $table) {
            $table->id();
            $table->string('provider_content_id');
            $table->string('external_resource_id');
            $table->string('type');
            $table->integer('provider_platform_id')->references('id')->on('provider_platforms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_contents');
    }
};
