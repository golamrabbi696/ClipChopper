<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_settings', function (Blueprint $table) {
            $table->id();
            $table->text('discord_webhook_url')->nullable();
            $table->boolean('discord_enabled')->default(false);
            $table->string('telegram_bot_token')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->boolean('telegram_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_settings');
    }
};

