<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('plan')->nullable(); // one-time | monthly | brands | unsure
            $table->text('message')->nullable();
            $table->string('source')->default('website');
            $table->boolean('newsletter_opt_in')->default(false);
            $table->string('status')->default('new'); // new | contacted | booked | lost
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('leads'); }
};
