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
        Schema::table('daily_salaries', function (Blueprint $table) {
            $table->boolean('paid')->nullable()->default(0)->after('payment_batch_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_salaries', function (Blueprint $table) {
            $table->dropColumn(['paid']);
        });
    }
};
