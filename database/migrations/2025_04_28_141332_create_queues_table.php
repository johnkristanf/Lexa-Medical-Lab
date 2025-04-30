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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number');
            $table->string('name')->nullable();
            $table->timestamp('called_at')->nullable();

            $table->foreignId('status_id')->constrained('queue_statuses')->default(1);
            $table->foreignId('priority_type_id')->constrained('priority_types');
            $table->foreignId('counter_id')->nullable()->constrained('counters');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
