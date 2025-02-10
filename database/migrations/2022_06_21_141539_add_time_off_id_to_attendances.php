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
        Schema::table('attendances', function (Blueprint $table) {
            $table->after('long_shift_working_pattern_id', function (Blueprint $table) {
                $table->foreignId('sick_application_id')
                    ->nullable()
                    ->constrained('sick_applications')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('permission_application_id')
                    ->nullable()
                    ->constrained('permission_applications')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreignId('leave_application_id')
                    ->nullable()
                    ->constrained('leave_applications')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
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
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
};
