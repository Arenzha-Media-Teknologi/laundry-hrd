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
        Schema::table('payroll_bca_email_logs', function (Blueprint $table) {
            $table->after('subject', function (Blueprint $table) {
                $table->string('transaction_file_name')->nullable();
                $table->string('checksum_file_name')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_bca_email_logs', function (Blueprint $table) {
            $table->dropColumn(['transaction_file_name', 'checksum_file_name']);
        });
    }
};
