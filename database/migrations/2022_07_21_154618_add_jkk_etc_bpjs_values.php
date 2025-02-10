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
        Schema::table('bpjs_values', function (Blueprint $table) {
            $table->after('year', function (Blueprint $table) {
                $table->integer('jht_payment');
                $table->integer('jkk_payment');
                $table->integer('jkm_payment');
                $table->integer('jp_payment');
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
        Schema::table('bpjs_values', function (Blueprint $table) {
            $table->dropColumn(['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment']);
        });
    }
};
