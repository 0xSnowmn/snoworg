<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditActivatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activates', function (Blueprint $table) {
            $table->string('last_used')->nullable();
            $table->string('expire')->nullable()->change();
            $table->string('version');
            $table->string('status')->default('waiting');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activates', function (Blueprint $table) {
            //
        });
    }
}
