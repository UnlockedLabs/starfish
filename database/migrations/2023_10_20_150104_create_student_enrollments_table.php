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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->integer('provider_user_id')->references('provider_user_id')->on('student_mappings');
            $table->foreignId('provider_content_id')->references('id')->on('provider_contents');
            $table->foreignId('provider_platform_id')->constrained();
            $table->string('status')->default('not_completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
