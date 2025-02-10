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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            // Clock In
            $table->time('clock_in_time')->nullable();
            $table->dateTime('clock_in_at')->nullable();
            $table->string('clock_in_ip_address', 20)->nullable();
            $table->string('clock_in_device_detail', 255)->nullable();
            $table->string('clock_in_latitude', 30)->nullable();
            $table->string('clock_in_longitude', 255)->nullable();
            $table->string('clock_in_office_latitude', 255)->nullable();
            $table->string('clock_in_office_longitude', 255)->nullable();
            $table->time('clock_in_working_pattern_time')->nullable();
            $table->string('clock_in_attachment', 255)->nullable();
            $table->string('clock_in_note', 255)->nullable();
            // Clock Out
            $table->time('clock_out_time')->nullable();
            $table->dateTime('clock_out_at')->nullable();
            $table->string('clock_out_ip_address', 20)->nullable();
            $table->string('clock_out_device_detail', 255)->nullable();
            $table->string('clock_out_latitude', 30)->nullable();
            $table->string('clock_out_longitude', 255)->nullable();
            $table->string('clock_out_office_latitude', 255)->nullable();
            $table->string('clock_out_office_longitude', 255)->nullable();
            $table->time('clock_out_working_pattern_time')->nullable();
            $table->string('clock_out_attachment', 255)->nullable();
            $table->string('clock_out_note', 255)->nullable();

            $table->string('status', 30)->nullable();
            $table->smallInteger('time_late')->nullable();
            $table->smallInteger('early_leaving')->nullable();
            $table->smallInteger('overtime')->nullable();
            $table->string('approval_status', 30)->nullable();
            $table->boolean('is_long_shift')->nullable()->default(false);
            $table->time('long_shift_working_pattern_clock_in_time')->nullable();
            $table->time('long_shift_working_pattern_clock_out_time')->nullable();
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('working_pattern_id')
                ->nullable()
                ->constrained('working_patterns')
                ->onDelete('NO ACTION')
                ->cascadeOnUpdate();
            $table->foreignId('long_shift_working_pattern_id')
                ->nullable()
                ->constrained('working_patterns')
                ->onDelete('NO ACTION')
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
        Schema::dropIfExists('attendances');
    }
};
