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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->string('check_in_ip_address', 20)->nullable();
            $table->string('check_in_device_detail', 255)->nullable();
            $table->string('check_in_latitude', 30)->nullable();
            $table->string('check_in_longitude', 255)->nullable();
            $table->string('check_in_location', 255)->nullable();
            $table->tinyInteger('check_in_is_inside_office_radius')->default(0);
            $table->string('check_in_attachment', 255)->nullable();
            $table->string('check_in_note', 255)->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('check_out_ip_address', 20)->nullable();
            $table->string('check_out_device_detail', 255)->nullable();
            $table->string('check_out_latitude', 30)->nullable();
            $table->string('check_out_longitude', 255)->nullable();
            $table->string('check_out_location', 255)->nullable();
            $table->tinyInteger('check_out_is_inside_office_radius')->default(0);
            $table->string('check_out_attachment', 255)->nullable();
            $table->string('check_out_note', 255)->nullable();
            $table->bigInteger('employee_id')->unsigned();
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
        Schema::dropIfExists('activities');
    }
};
