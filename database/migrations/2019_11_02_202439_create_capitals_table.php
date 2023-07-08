<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapitalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capitals', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->unique()->primary();

            $table->string('branch_id', 36);
            $table->string('method_id', 36);
            $table->string('fiscal_year_id', 36)->nullable();
            $table->string('amount');
            $table->date('capital_date');
            $table->string('description')->nullable();
            $table->string('created_by', 36)->nullable();

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
        Schema::dropIfExists('capitals');
    }
}
