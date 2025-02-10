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
        Schema::table('employees', function (Blueprint $table) {
            $table->after('aerplus_daily_salary', function (Blueprint $table) {
                $table->string('npwp_number')->nullable();
                $table->string('npwp_effective_date')->nullable();
                $table->string('npwp_status')->nullable();
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
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['npwp_number', 'npwp_effective_date', 'npwp_status']);
        });
    }
};
