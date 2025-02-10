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
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('ip_address', 20)->nullable();
            $table->string('device_detail', 255)->nullable();
            $table->string('latitude', 30)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->string('office_latitude', 255)->nullable();
            $table->string('office_longitude', 255)->nullable();
            $table->tinyInteger('is_inside_office_radius')->default(0);
            $table->time('working_pattern_time')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->string('note', 255)->nullable();
            $table->string('status', 30)->nullable();
            $table->smallInteger('time_late')->nullable();
            $table->smallInteger('early_leaving')->nullable();
            $table->smallInteger('overtime')->nullable();
            $table->string('approval_status', 30)->nullable();
            $table->tinyInteger('is_long_shift')->default(0);
            $table->bigInteger('employee_id')->unsigned();
            $table->bigInteger('working_pattern_id')->unsigned()->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('check_ins');
    }
};
