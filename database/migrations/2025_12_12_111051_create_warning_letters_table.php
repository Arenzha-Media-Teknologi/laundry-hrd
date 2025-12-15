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
        Schema::create('warning_letters', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->unique();
            $table->foreignId('employee_id');
            $table->date('effective_start_date')->nullable();
            $table->date('effective_end_date')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('signatory')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warning_letters');
    }
};
