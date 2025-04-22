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
        Schema::table('work_schedule_working_patterns', function (Blueprint $table) {
            $table->after('color', function (Blueprint $table) {
                $table->boolean('have_overtime')->nullable()->default(0);
                $table->string('overtime_start_time')->nullable();
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
        Schema::table('work_schedule_working_patterns', function (Blueprint $table) {
            $table->dropColumn(['have_overtime', 'overtime_start_time']);
        });
    }
};
