<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->string('language')->default('php');
            $table->text('source_code');
            $table->enum('status',['queued','running','evaluated','error'])->default('queued');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('submissions');
    }
};
