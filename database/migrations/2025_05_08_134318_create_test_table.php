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
        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->string('referer_fullname');
            $table->string('doctor_license_no')->nullable();
            $table->date('test_schedule');
            $table->integer('total_price');
            $table->enum('status', ['paid', 'pending'])->default('pending');
            $table->foreignId('purpose_id')->constrained('test_purpose')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('test_category')->onDelete('cascade');
            $table->json('selected_test_types')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test');
    }
};
