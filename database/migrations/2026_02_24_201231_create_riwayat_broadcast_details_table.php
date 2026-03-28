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
        Schema::create('riwayat_broadcast_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riwayat_broadcast_id')->constrained('riwayat_broadcasts')->onDelete('cascade');
            $table->string('no_wa');
            $table->string('status')->default('pending'); 
            $table->string('fonnte_message_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_broadcast_details');
    }
};
