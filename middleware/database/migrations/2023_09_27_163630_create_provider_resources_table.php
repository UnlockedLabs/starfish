<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('provider_resources', function (Blueprint $table) {
            $table->id();
            $table->foreign('resource_id')->constrained();
            $table->integer('external_resource_id');
            $table->string('provider_resource_type');
            $table->foreign('provider_platform_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('provider_resources');
    }
}
