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
        Schema::table('employee_bpjs', function (Blueprint $table) {
            $table->string('ketenagakerjaan_number')->nullable()->change();
            $table->integer('ketenagakerjaan_start_year')->nullable()->change();
            $table->string('mandiri_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_bpjs', function (Blueprint $table) {
            $table->string('ketenagakerjaan_number')->change();
            $table->integer('ketenagakerjaan_start_year')->change();
            $table->string('mandiri_number')->change();
        });
    }
};
