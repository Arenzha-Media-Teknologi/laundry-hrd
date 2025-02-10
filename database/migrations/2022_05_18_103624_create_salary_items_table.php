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
        Schema::create('salary_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('salary_type');
            $table->integer('amount');
            $table->foreignId('salary_id')
                ->constrained('salaries')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('loan_item_id')
                ->nullable()
                ->default(null)
                ->constrained('loan_items')
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
        Schema::dropIfExists('salary_items');
    }
};
