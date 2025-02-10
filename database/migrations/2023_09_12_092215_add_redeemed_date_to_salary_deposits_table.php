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
        Schema::table('salary_deposits', function (Blueprint $table) {
            $table->date('redeemed_date')->nullable()->after('redeemed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_deposits', function (Blueprint $table) {
            $table->dropColumn(['redeemed_date']);
        });
    }
};
