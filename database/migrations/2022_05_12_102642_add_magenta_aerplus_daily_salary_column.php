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
            $table->after('office_id', function (Blueprint $table) {
                $table->boolean('magenta_daily_salary')->nullable()->default(false);
                $table->boolean('aerplus_daily_salary')->nullable()->default(false);
            });
            $table->renameColumn('nonactive_at', 'inactive_at');
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
            $table->dropColumn(['magenta_daily_salary', 'daily_daily_salary']);
            $table->renameColumn('inactive_at', 'nonactive_at');
        });
    }
};
