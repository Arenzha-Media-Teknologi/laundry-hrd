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
        Schema::create('work_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->boolean('is_off')->nullable();
            $table->foreignId('employee_id')->nullable();
            $table->foreignId('work_schedule_working_pattern_id')->nullable();
            $table->foreignId('work_schedule_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('work_schedule_items');
    }
};
