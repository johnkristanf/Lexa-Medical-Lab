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
        Schema::table('test', function (Blueprint $table) {
            $table->string('or_number')->nullable()->after('total_price'); // or after any column you prefer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test', function (Blueprint $table) {
            $table->dropColumn('or_number');
        });
    }
};
