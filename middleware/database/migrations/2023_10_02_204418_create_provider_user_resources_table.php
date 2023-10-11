<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
    For all intents and purposes. This is an "Enrollment"

    user_id: Integer
    provider_resource_id: Integer
    provider_id: Integer
    status: enum ProviderUserResourceStatus
     */
    public function up(): void
    {
        Schema::create('provider_user_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('provider_resource_id')->constrained();
            $table->foreignId('provider_id')->constrained();
            $table->string('status')->default('not_completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_user_resources');
    }
};
