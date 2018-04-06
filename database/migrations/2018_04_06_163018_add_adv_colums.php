<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvColums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mems', function (Blueprint $table) {
           $table->boolean('isMem')->default(false);
           $table->integer('commentCount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mems', function (Blueprint $table) {
            $table->dropColumn(['commentCount','isMem']);
        });
    }
}
