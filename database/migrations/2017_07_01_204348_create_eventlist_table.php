<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EVENTLISTS', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title')->nullable();
            $table->date('date')->nullable();
            $table->string('people')->nullable();
            $table->text('detail_url')->nullable();
            $table->text('detail')->nullable();
            $table->datetime('last_tweet_date')->nullable();
            //$table->integer('votes');
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
        Schema::dropIfExists('EVENTLISTS');
    }
}
