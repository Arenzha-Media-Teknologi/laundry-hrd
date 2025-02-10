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
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('clock_in_is_inside_office_radius')->nullable()->default(0)->after('clock_in_office_latitude');
            $table->boolean('clock_out_is_inside_office_radius')->nullable()->default(0)->after('clock_out_office_latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['clock_in_is_inside_office_radius', 'clock_out_is_inside_office_radius']);
        });
    }
};
