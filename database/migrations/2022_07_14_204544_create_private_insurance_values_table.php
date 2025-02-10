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
        Schema::create('private_insurance_values', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->integer('total_premi')->nullable()->default(0);
            $table->integer('kesehatan')->nullable()->default(0);
            $table->integer('nilai_tabungan')->nullable()->default(0);
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('private_insurance_id')
                ->constrained('private_insurances')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('private_insurance_values');
    }
};
