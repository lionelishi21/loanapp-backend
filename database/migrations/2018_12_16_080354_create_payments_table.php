<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();
            $table->string('branch_id', 36);

            $table->string('member_id')->nullable();
            $table->string('amount');
            $table->string('method_id')->nullable();

            $table->string('transaction_id')->nullable();
            $table->date('payment_date');
            $table->string('receipt_number')->nullable();
            $table->string('attachment')->nullable();
            $table->string('notes')->nullable();

            //bank fields
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->date('cheque_date')->nullable();

            // Mpesa fields
            $table->boolean('is_mpesa')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('trans_id')->nullable()->unique();
            $table->string('trans_time')->nullable();
            $table->string('business_short_code')->nullable();
            $table->string('bill_ref_number')->nullable();
            $table->string('invoice_number')->nullable();

            $table->string('mpesa_number')->nullable();
            $table->string('mpesa_first_name')->nullable();
            $table->string('mpesa_middle_name')->nullable();
            $table->string('mpesa_last_name')->nullable();

            $table->string('org_account_balance')->nullable();
            $table->string('third_party_trans_id')->nullable();

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
        Schema::dropIfExists('payments');
    }
}
