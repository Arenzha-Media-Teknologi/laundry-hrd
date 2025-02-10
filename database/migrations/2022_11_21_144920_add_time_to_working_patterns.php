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
        Schema::table('working_patterns', function (Blueprint $table) {
            $table->after('division', function (Blueprint $table) {
                $table->string('time')->nullable();
                $table->string('time_description')->nullable();
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
        Schema::table('working_patterns', function (Blueprint $table) {
            $table->dropColumn(['time', 'time_description']);
        });
    }
};
