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
        Schema::table('salary_items', function (Blueprint $table) {
            $table->after('salary_type', function (Blueprint $table) {
                $table->decimal('salary_type_amount')->nullable();
                $table->string('salary_type_unit')->nullable();
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
        Schema::table('salary_items', function (Blueprint $table) {
            $table->dropColumn(['salary_type_amount', 'salary_type_unit']);
        });
    }
};
