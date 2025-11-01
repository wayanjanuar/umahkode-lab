<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('template_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // sesuaikan dengan kondisi awal kamu
            $table->string('template_path')->default('')->nullable(false)->change();
        });
    }
};
