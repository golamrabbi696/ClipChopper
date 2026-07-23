<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('call_date')->nullable()->after('user_id');
            $table->time('call_time')->nullable()->after('call_date');
            $table->string('call_timezone', 100)->nullable()->after('call_time');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['call_date', 'call_time', 'call_timezone']);
        });
    }
};

