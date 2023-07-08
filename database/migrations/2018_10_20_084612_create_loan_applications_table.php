<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            $table->string('branch_id', 36);

            $table->string('member_id', 36);
            $table->string('loan_officer_id', 36);

            $table->string('loan_type_id', 36);
            $table->string('interest_type_id', 36)->nullable();
            $table->string('interest_rate')->nullable();
            $table->double('service_fee')->default(0);

            $table->string('penalty_type_id', 36)->nullable()->default('');
            $table->double('penalty_value')->default(0);
            $table->string('penalty_frequency_id', 36)->nullable()->default('');

            $table->boolean('reduce_principal_early')->default(false);

            $table->string('amount_applied');
            $table->string('repayment_period')->nullable();

            $table->string('payment_frequency_id', 36)->nullable();
            $table->string('periodic_payment_amount')->nullable(); //**

            $table->date('application_date');
            $table->string('witness_type_id', 36)->nullable();
            $table->string('witness_first_name')->nullable();
            $table->string('witness_last_name')->nullable();
            $table->string('witness_country')->nullable();
            $table->string('witness_county')->nullable();
            $table->string('witness_city')->nullable();
            $table->string('witness_national_id')->nullable();
            $table->string('witness_phone')->nullable();
            $table->string('witness_email')->nullable();
            $table->string('witness_postal_address')->nullable();
            $table->string('witness_residential_address')->nullable();

            $table->string('disburse_method_id')->nullable();

            //mpesa fields
            $table->string('mpesa_number')->nullable();
            $table->string('mpesa_first_name')->nullable();
            $table->string('mpesa_middle_name')->nullable();
            $table->string('mpesa_last_name')->nullable();

            //bank fields
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->date('cheque_date')->nullable();

            $table->string('disburse_note')->nullable();

            $table->string('status_id', 36)->nullable();
            $table->string('witnessed_by_user_id', 36)->nullable();
            $table->string('reviewed_by_user_id', 36)->nullable();
            $table->string('reviewed_on')->nullable();
            $table->string('approved_on')->nullable();
            $table->string('rejected_on')->nullable();
            $table->string('rejection_notes')->nullable();
            $table->string('attach_application_form')->nullable();

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
        Schema::dropIfExists('loan_applications');
    }
}
