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
            $table->after('number_of_days', function (Blueprint $table) {
                $table->string('division')->nullable()->default('all');
                $table->boolean('default')->nullable()->default(false);
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
            $table->dropColumn(['division', 'default']);
        });
    }
};
