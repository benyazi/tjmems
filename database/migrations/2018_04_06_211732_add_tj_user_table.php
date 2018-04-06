<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTjUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tj_users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('tjId')->unique();
            $table->timestamp('created')->nullable();

            $table->string('name',255)->nullable();
            $table->string('avatarUrl',255)->nullable();
            $table->integer('karma')->nullable()->index();
            $table->integer('entryCount')->nullable()->index();
            $table->integer('commentCount')->nullable()->index();
            $table->integer('favoriteCount')->nullable()->index();

            $table->boolean('isAdmin')->default(false);
            $table->boolean('isSubscriptionActive')->default(false);

            $table->text('tjObject')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tj_users');
    }
}
