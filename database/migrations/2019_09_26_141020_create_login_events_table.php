<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_events', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->index();

            $table->string('user_id')->index()->nullable();
            $table->string('event');
            $table->string('email')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_events');
    }
}
