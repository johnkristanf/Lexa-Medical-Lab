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
        Schema::table('patient_test_type', function (Blueprint $table) {
            $table->unsignedBigInteger('test_id')->nullable()->after('test_type_id');

            $table->foreign('test_id')
                ->references('id')
                ->on('test')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_test_type', function (Blueprint $table) {
            $table->dropForeign(['test_id']);
            $table->dropColumn('test_id');
        });
    }
};
