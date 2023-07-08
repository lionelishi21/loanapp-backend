<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            $table->string('protocol')->nullable();
            $table->string('smpt_host')->nullable();
            $table->string('smpt_username')->nullable();
            $table->string('smpt_password')->nullable();
            $table->string('smpt_port')->nullable();
            $table->string('mail_gun_domain')->nullable();
            $table->string('mail_gun_secret')->nullable();
            $table->string('mandrill_secret')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('email_settings');
    }
}
