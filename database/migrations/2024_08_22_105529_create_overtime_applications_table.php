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
        Schema::create('overtime_applications', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->string('job_order_number')->nullable();
            $table->string('order')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('customer')->nullable();
            $table->string('order_quantity')->nullable();
            $table->string('difference_note')->nullable();
            $table->foreignId('prepared_by')->nullable();
            $table->foreignId('submitted_by')->nullable();
            $table->foreignId('known_by')->nullable();
            $table->foreignId('created_by')->nullable();
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
        Schema::dropIfExists('overtime_applications');
    }
};
