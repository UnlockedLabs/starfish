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
            $table->integer('resource_id');
            $table->integer('external_resource_id');
            $table->string('provider_resource_type'); // You can change the type to 'enum' if needed.
            $table->integer('provider_platform_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('provider_resources');
    }
}
