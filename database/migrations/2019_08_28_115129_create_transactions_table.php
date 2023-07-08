<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            $table->string('branch_id', 36);
            $table->string('fiscal_period_id', 36)->nullable();
            $table->string('loan_id', 36);

            $table->string('payment_id', 36)->nullable(); // ->nullable() due to waivers
            $table->string('amount');
            $table->date('transaction_date');

            $table->string('loan_interest_repayments_id', 36)->nullable();
            $table->string('loan_principal_repayments_id', 36)->nullable();
            $table->string('loan_penalties_id', 36)->nullable();

            $table->enum('transaction_type',
                [
                    'interest_payment',
                    'principal_payment',
                    'penalty_payment',
                    'penalty_waiver',
                    'balance_reduction',
                ])->default(null)->nullable();

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
        Schema::dropIfExists('transactions');
    }
}
