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
        Schema::table('work_schedule_items', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable()->after('work_schedule_working_pattern_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_schedule_items', function (Blueprint $table) {
            $table->dropColumn(['office_id']);
        });
    }
};
