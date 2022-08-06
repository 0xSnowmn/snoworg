<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('active_request');
            $table->string('user')->nullable();
            $table->string('pass')->nullable();
            $table->string('mac')->nullable();
            $table->boolean('used')->default(false);
            $table->string('activate_at')->nullable();
            $table->string('expire');
            $table->unsignedInteger('program');
            $table->foreign('program')->references('id')->on('programs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activates');
    }
}
