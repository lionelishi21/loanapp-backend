<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();

            $table->string('branch_id', 36);
            $table->string('loan_reference_number')->nullable();

            $table->string('loan_application_id', 36)->unique();
            $table->string('member_id', 36);
            $table->string('loan_officer_id', 36);

            $table->string('loan_type_id', 36)->nullable();
            $table->string('interest_rate');
            $table->string('interest_type_id', 36);
            $table->string('repayment_period');
            $table->string('loan_status_id')->nullable();
            $table->string('approved_by_user_id')->nullable();

            $table->string('amount_approved');
            $table->string('disburse_amount');
            $table->double('service_fee')->default(0);

            $table->string('penalty_type_id', 36)->nullable()->default('');
            $table->double('penalty_value')->default(0);
            $table->string('penalty_frequency_id', 36)->nullable()->default('');

            $table->boolean('loan_disbursed')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('payment_frequency_id', 36);

            $table->boolean('reduce_principal_early')->default(false);

            $table->date('next_repayment_date')->nullable();

            $table->string('disburse_method_id')->nullable();

            $table->string('mpesa_number')->nullable();
            $table->string('mpesa_first_name')->nullable();
            $table->string('mpesa_middle_name')->nullable();
            $table->string('mpesa_last_name')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('other_banking_details')->nullable();

            $table->timestamp('closed_on')->nullable();

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
        Schema::dropIfExists('loans');
    }
}
