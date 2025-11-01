<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('final_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ringkasan per user
            $table->unsignedTinyInteger('completed_assignments')->default(0); // berapa soal yang sudah dinilai
            $table->unsignedTinyInteger('total_assignments')->default(0);     // total soal di sistem saat generate

            $table->decimal('average_percent', 6, 2)->nullable(); // 0..100
            $table->unsignedTinyInteger('scale_1_5')->nullable(); // 1..5 (hanya jika sudah submit semua)

            // detail per assignment (nilai per-soal & komponen)
            $table->json('details')->nullable();

            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id']); // 1 baris per user (terakhir overwrite)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_scores');
    }
};
