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
        Schema::table('check_ins', function (Blueprint $table) {
            // RENAME COLUMN
            $table->renameColumn('ip_address', 'check_in_ip_address');
            $table->renameColumn('device_detail', 'check_in_device_detail');
            $table->renameColumn('latitude', 'check_in_latitude');
            $table->renameColumn('longitude', 'check_in_longitude');
            $table->renameColumn('location', 'check_in_location');
            $table->renameColumn('is_inside_office_radius', 'check_in_is_inside_office_radius');
            $table->renameColumn('attachment', 'check_in_attachment');
            $table->renameColumn('note', 'check_in_note');

            // ADD COLUMN
            $table->after('note', function (Blueprint $table) {
                $table->string('check_out_ip_address', 20)->nullable();
                $table->string('check_out_device_detail', 255)->nullable();
                $table->string('check_out_latitude', 30)->nullable();
                $table->string('check_out_longitude', 255)->nullable();
                $table->string('check_out_location', 255)->nullable();
                $table->tinyInteger('check_out_is_inside_office_radius')->default(0);
                $table->string('check_out_attachment', 255)->nullable();
                $table->string('check_out_note', 255)->nullable();
            });
            // DROP COLUMN
            $table->dropColumn([
                'office_latitude',
                'office_longitude',
                'working_pattern_time',
                'status',
                'time_late',
                'early_leaving',
                'overtime',
                'approval_status',
                'is_long_shift',
                'working_pattern_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_ins', function (Blueprint $table) {
            // RENAME COLUMN
            $table->renameColumn('check_in_ip_address', 'ip_address');
            $table->renameColumn('check_in_device_detail', 'device_detail');
            $table->renameColumn('check_in_latitude', 'latitude');
            $table->renameColumn('check_in_longitude', 'longitude');
            $table->renameColumn('check_in_is_inside_office_radius', 'is_inside_office_radius');
            $table->renameColumn('check_in_attachment', 'attachment');
            $table->renameColumn('check_in_note', 'note');

            // ADD COLUMN
            $table->string('office_latitude', 255)->nullable();
            $table->string('office_longitude', 255)->nullable();
            $table->time('working_pattern_time')->nullable();
            $table->string('status', 30)->nullable();
            $table->smallInteger('time_late')->nullable();
            $table->smallInteger('early_leaving')->nullable();
            $table->smallInteger('overtime')->nullable();
            $table->string('approval_status', 30)->nullable();
            $table->tinyInteger('is_long_shift')->default(0);
            $table->bigInteger('working_pattern_id')->unsigned()->nullable();

            // DROP COLUMN
            $table->dropColumn([
                'check_out_ip_address',
                'check_out_device_detail',
                'check_out_latitude',
                'check_out_longitude',
                'check_out_is_inside_office_radius',
                'check_out_attachment',
                'check_out_note',
            ]);
        });
    }
};
