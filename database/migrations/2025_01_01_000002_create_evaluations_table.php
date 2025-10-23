<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('grader_id')->nullable();
            $table->integer('score')->default(0);
            $table->json('breakdown')->nullable();
            $table->text('feedback')->nullable();
            $table->json('artifacts')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('evaluations');
    }
};
