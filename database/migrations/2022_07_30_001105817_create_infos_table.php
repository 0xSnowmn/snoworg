<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('activates')->onDelete('cascade');
            $table->string('mac_adress')->nullable();
            $table->string('version')->nullable();
            $table->unsignedInteger('program');
            $table->foreign('program')->references('id')->on('programs')->onDelete('cascade');
            $table->string('os')->nullable();
            $table->string('Last_opened')->default(Carbon::now('Africa/Cairo'));
            $table->string('Pc_name')->nullable();
            $table->bigInteger('count_using')->default(1);
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
        Schema::dropIfExists('infos');
    }
}
