<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->json('rubric')->nullable();            // rubrik khusus soal (bobot & kriteria)
            $table->json('expected_patterns')->nullable(); // regex/kata kunci yang diharapkan
            $table->json('forbidden_patterns')->nullable();// pola yang dilarang (e.g. echo $_GET tanpa esc)
            $table->json('test_cases')->nullable();        // optional: input→output yang diharapkan
            $table->string('rubric_version')->nullable();  // metadata versi rubrik
        });
    }
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['rubric', 'expected_patterns', 'forbidden_patterns', 'test_cases', 'rubric_version']);
        });
    }

};
