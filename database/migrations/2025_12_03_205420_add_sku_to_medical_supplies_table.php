<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
        Schema::table('medical_supplies', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('expiration_date');
        });
    }

    public function down()
    {
        Schema::table('medical_supplies', function (Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
};
