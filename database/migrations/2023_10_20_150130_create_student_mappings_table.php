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
        Schema::create('student_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('student_id')->constrained();
            $table->integer('provider_user_id')->references('provider_user_id')->on('student_enrollments');
            $table->foreignId('provider_platform_id')->constrained();
            $table->integer('consumer_user_id');
            $table->foreignId('consumer_platform_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_mappings');
    }
};
