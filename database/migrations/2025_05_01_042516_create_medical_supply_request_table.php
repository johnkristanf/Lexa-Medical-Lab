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
        Schema::create('medical_supply_request', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->foreignId('request_id')->constrained('supply_requests')->onDelete('cascade');
            $table->foreignId('supply_id')->constrained('medical_supplies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_supply_request');
    }
};
