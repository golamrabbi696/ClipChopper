<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('month'); // e.g. "2026-07"
            $table->string('type'); // video | post | quote
            $table->string('title');
            $table->string('file_url')->nullable();
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('content_deliverables'); }
};
