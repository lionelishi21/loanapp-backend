<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();
            $table->string('branch_id', 36);

            $table->string('employee_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('salutation')->nullable();

            $table->string('email')->unique();
            $table->string('telephone_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('national_id_image')->nullable();

            $table->string('country')->nullable();
            $table->string('county')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();

            $table->string('job_group')->nullable();
            $table->string('nhif_number')->nullable();
            $table->string('nssf_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('kra_pin')->nullable();

            $table->string('national_id_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('designation_id')->nullable();
            $table->string('department_id')->nullable();
            $table->string('birth_day')->unique();

            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->string('deleted_by', 36)->nullable();

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
        Schema::dropIfExists('employees');
    }
}
