<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test_selected_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('test')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('test_category')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing category IDs from test table to the new pivot table
        $tests = DB::table('test')->whereNotNull('category_id')->get();
        $recordsToInsert = [];
        foreach ($tests as $test) {
            $recordsToInsert[] = [
                'test_id' => $test->id,
                'category_id' => $test->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($recordsToInsert)) {
            DB::table('test_selected_categories')->insert($recordsToInsert);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_selected_categories');
    }
};
