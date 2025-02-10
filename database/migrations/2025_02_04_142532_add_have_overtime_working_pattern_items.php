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
        Schema::table('working_pattern_items', function (Blueprint $table) {
            $table->after('delay_tolerance', function (Blueprint $table) {
                $table->boolean('have_overtime')->nullable();
                $table->time('overtime_start_time')->nullable();
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
        Schema::table('working_pattern_items', function (Blueprint $table) {
            $table->dropColumn(['have_overtime', 'overtime_start_time']);
        });
    }
};
