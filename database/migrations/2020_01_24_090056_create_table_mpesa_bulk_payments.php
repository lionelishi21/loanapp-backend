<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMpesaBulkPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_bulk_payments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('transaction_amount'); //100
            $table->string('transaction_receipt')->unique(); //LHL41AHJ6G
            $table->string('b2C_recipientIs_registered_customer'); //Y
            $table->string('b2C_charges_paid_account_available_funds'); //0.00
            $table->string('receiver_party_public_name'); //254708374149 - John Doe
            $table->string('transaction_completed_date_time'); //"21.08.2017 12:01:59"
            $table->string('b2C_utility_account_available_funds'); //98834.00
            $table->string('b2C_working_account_available_funds'); //100000.00

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
        Schema::dropIfExists('mpesa_bulk_payments');
    }
}
