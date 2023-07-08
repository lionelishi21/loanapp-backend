<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaCustomSendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_custom_sends', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('phone');
            $table->string('amount')->unique();
            $table->string('description')->unique();

            // Mpesa fields
            $table->date('transaction_type')->nullable();
            $table->date('trans_id')->nullable()->unique();
            $table->date('trans_time')->nullable();
            $table->date('business_short_code')->nullable();
            $table->date('bill_ref_number')->nullable();
            $table->date('invoice_number')->nullable();
            $table->date('phone_number')->nullable();
            $table->date('first_name')->nullable();
            $table->date('middle_name')->nullable();
            $table->date('last_name')->nullable();
            $table->date('org_account_balance')->nullable();
            $table->date('third_party_trans_id')->nullable();

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
        Schema::dropIfExists('mpesa_custom_sends');
    }
}
