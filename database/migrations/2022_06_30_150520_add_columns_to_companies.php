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
        Schema::table('companies', function (Blueprint $table) {
            $table->after('address', function (Blueprint $table) {
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('npwp_number')->nullable();
                $table->string('npwp_address')->nullable();
                $table->foreignId('president_director')
                    ->nullable()
                    ->constrained('employees')
                    ->onDelete('NO ACTION')
                    ->onUpdate('CASCADE');
                $table->foreignId('director')
                    ->nullable()
                    ->constrained('employees')
                    ->onDelete('NO ACTION')
                    ->onUpdate('CASCADE');
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
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
