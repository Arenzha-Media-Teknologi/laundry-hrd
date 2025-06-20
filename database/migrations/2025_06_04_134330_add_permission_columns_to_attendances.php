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
            $table->after('long_shift_confirmed_at', function (Blueprint $table) {
                $table->boolean('is_permission')->nullable()->default(0);
                $table->foreignId('permission_category_id')->nullable();
                $table->string('permission_note')->nullable();
                $table->string('permission_status')->nullable();
                $table->foreignId('permission_confirmed_by')->nullable();
                $table->dateTime('permission_confirmed_at')->nullable();
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
                'is_permission',
                'permission_category_id',
                'permission_note',
                'permission_status',
                'permission_confirmed_by',
                'permission_confirmed_at',
            ]);
        });
    }
};
