<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->unique()->primary();

            $table->string('branch_id');
            $table->string('member_id');
            $table->string('amount');
            $table->string('withdrawal_date');
            $table->string('method_id');
            $table->string('withdrawal_number');

            $table->string('withdrawal_charges')->nullable();
            $table->string('balance_before_withdrawal')->nullable();
            $table->string('balance_after_withdrawal')->nullable();
            $table->string('status')->nullable();

            $table->string('mpesa_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            //bank fields
            $table->string('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->date('cheque_date')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

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
        Schema::dropIfExists('withdrawals');
    }
}
