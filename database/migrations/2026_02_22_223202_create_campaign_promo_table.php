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
        Schema::create('campaign_promo', function (Blueprint $table) {
            $table->id('id_campaign');
            $table->string('nama_campaign');
            $table->string('gambar_banner')->nullable();
            $table->date('tanggal_mulai');    // Ubah ke date
            $table->date('tanggal_selesai');  // Ubah ke date
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif'); // Samakan dengan enum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_promo');
    }
};
