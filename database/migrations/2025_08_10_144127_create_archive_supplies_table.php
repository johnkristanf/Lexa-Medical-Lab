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
        Schema::create('archive_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_supplies_id')
                ->nullable()
                ->constrained('medical_supplies')
                ->nullOnDelete();
            $table->foreignId('batch_id')
                ->nullable()
                ->constrained('batches')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_supplies');
    }
};
