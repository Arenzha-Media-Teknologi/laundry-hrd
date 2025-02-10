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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('number', 50);
            $table->string('gender', 10);
            $table->string('place_of_birth', 255);
            $table->date('date_of_birth');
            $table->string('identity_type', 10);
            $table->string('identity_number', 50);
            $table->string('marital_status', 50)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('recent_education', 10)->nullable();
            $table->string('education_institution_name', 255)->nullable();
            $table->string('study_program', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_relation', 255)->nullable();
            $table->string('emergency_contact_phone', 50)->nullable();
            $table->date('start_work_date')->nullable();
            $table->string('photo', 255)->nullable();
            $table->boolean('active')->nullable()->default(true);
            $table->dateTime('nonactive_at')->nullable();
            $table->dateTime('active_at')->nullable();
            // $table->foreignId('company_id')
            //     ->constrained('companies')
            //     ->restrictOnDelete()
            //     ->restrictOnUpdate();
            // $table->foreignId('division_id')
            //     ->constrained('divisions')
            //     ->restrictOnDelete()
            //     ->restrictOnUpdate();
            $table->foreignId('office_id')
                ->constrained('offices')
                ->restrictOnDelete()
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
        Schema::dropIfExists('employees');
    }
};
