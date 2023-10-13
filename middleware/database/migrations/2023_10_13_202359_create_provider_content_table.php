<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderContentTables extends Migration
{
    public function up()
    {
        Schema::create('provider_content', function (Blueprint $table) {
            $table->id();
            $table->integer('content_id');
            $table->integer('external_resource_id');
            $table->string('type');
            $table->foreign('provider_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('provider_content');
    }
}
