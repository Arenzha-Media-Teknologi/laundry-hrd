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
        Schema::create('working_pattern_items', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('order');
            $table->string('day_status', 30);
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->smallInteger('delay_tolerance')->nullable()->default(0);
            $table->foreignId('working_pattern_id')
                ->constrained('working_patterns')
                ->cascadeOnDelete()
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
        Schema::dropIfExists('working_pattern_items');
    }
};
