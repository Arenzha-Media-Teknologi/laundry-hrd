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
        Schema::create('permission_applications', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('application_dates');
            $table->string('note')->nullable();
            $table->string('approval_status', 30)->nullable()->default(1);
            $table->string('attachment')->nullable();
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('permission_category_id')
                ->constrained('permission_categories')
                ->onDelete('NO ACTION')
                ->cascadeOnUpdate();
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('confirmed_at')->nullable();
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
        Schema::dropIfExists('permission_applications');
    }
};
