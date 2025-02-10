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
        Schema::table('credential_groups', function (Blueprint $table) {
            $table->after('permissions', function (Blueprint $table) {
                $table->boolean('have_all_company_permissions')->nullable()->default(true);
                $table->string('company_permissions')->nullable()->default('[]');
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
        Schema::table('credential_groups', function (Blueprint $table) {
            //
        });
    }
};
