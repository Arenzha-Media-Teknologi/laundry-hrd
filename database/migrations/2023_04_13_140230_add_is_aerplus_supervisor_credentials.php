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
        Schema::table('credentials', function (Blueprint $table) {
            $table->after('accessible_designations', function (Blueprint $table) {
                $table->boolean('is_aerplus_supervisor')->default(0)->nullable();
                $table->string('accessible_offices', 500)->nullable();
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
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropColumn(['is_aerplus_supervisor', 'accessible_offices']);
        });
    }
};
