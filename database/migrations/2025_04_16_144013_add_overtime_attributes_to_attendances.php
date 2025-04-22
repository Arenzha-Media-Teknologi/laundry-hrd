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
            $table->after('overtime', function (Blueprint $table) {
                $table->string('overtime_approval_status')->nullable();
                $table->foreignId('overtime_confirmed_by')->nullable();
                $table->dateTime('overtime_confirmed_at')->nullable();
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
            $table->dropColumn([
                'overtime_approval_status',
                'overtime_confirmed_by',
                'overtime_confirmed_at',
            ]);
        });
    }
};
