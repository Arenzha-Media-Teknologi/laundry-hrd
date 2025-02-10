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
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->after('max_day', function (Blueprint $table) {
                $table->integer('max_advance_request_day')->nullable()->default(1);
                $table->boolean('allow_employee_submission')->nullable()->default(1);
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
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->dropColumn(['max_advance_request_day', 'allow_employee_submission']);
        });
    }
};
