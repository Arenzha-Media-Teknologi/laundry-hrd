<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_bca_email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sender');
            $table->string('to');
            $table->string('subject');
            $table->string('transaction_file')->nullable();
            $table->string('checksum_file')->nullable();
            $table->string('content')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('payroll_bca_email_logs');
    }
};
