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
        Schema::table('salaries', function (Blueprint $table) {
            $table->after('employee_id', function (Blueprint $table) {
                $table->boolean('paid')->nullable();
                $table->integer('last_paid_by')->nullable();
                $table->timestamp('last_paid_at')->nullable();
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
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['paid', 'last_paid_by', 'last_paid_at']);
        });
    }
};
