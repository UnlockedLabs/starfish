<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
    For all intents and purposes. This is an "Enrollment"

    UserId: Integer
    ProviderResourceId: Integer
    ProviderId: Integer
    Completed: boolean (we can check the date on the course object)
     */
    public function up(): void
    {
        Schema::create('provider_user_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('provider_resource_id')->constrained();
            $table->foreignId('provider_id')->constrained();
            $table->boolean('completed')->default(false);
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
