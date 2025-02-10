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
        Schema::create('employee_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('ketenagakerjaan_number');
            $table->year('ketenagakerjaan_start_year');
            $table->string('ketenagakerjaan_card_image')->nullable();
            $table->string('mandiri_number');
            $table->string('mandiri_card_image')->nullable();
            $table->foreignId('employee_id')
                ->constrained('employees')
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
        Schema::dropIfExists('employee_bpjs');
    }
};
