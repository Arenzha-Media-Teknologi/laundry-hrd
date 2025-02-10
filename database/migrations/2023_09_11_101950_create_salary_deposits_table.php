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
        Schema::create('salary_deposits', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('employee_id')->nullable();
            $table->integer('amount')->nullable();
            $table->smallInteger('installment')->nullable();
            $table->boolean('redeemed')->default(false)->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('salary_deposits');
    }
};
