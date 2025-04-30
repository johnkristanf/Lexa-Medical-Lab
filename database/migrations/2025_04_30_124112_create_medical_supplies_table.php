<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_supplies', function (Blueprint $table) {
            $table->id();
            $table->string('participants');
            $table->string('brand_name');
            $table->string('unit');
            $table->integer('quantity');
            $table->date('manufacture_date');
            $table->date('expiration_date');
            $table->string('lot_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_supplies');
    }
};
