<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();

            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('active_status')->nullable();
            $table->string('interest_rate')->nullable();
            $table->string('interest_type_id', 36);
            $table->string('payment_frequency_id', 36);
            $table->string('repayment_period')->nullable();
            $table->string('service_fee')->default(0);

            $table->boolean('reduce_principal_early')->default(false);

            $table->string('penalty_type_id', 36)->nullable()->default(''); // Fixed Amount, Percentage on Due
            $table->double('penalty_value')->default(0);
            $table->string('penalty_frequency_id', 36)->nullable()->default(''); // Daily, monthly etc

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
        Schema::dropIfExists('loan_types');
    }
}
