<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_members', function (Blueprint $table) {
            $table->id('id_paket'); // Primary Key
            $table->string('nama_paket');
            $table->enum('jenis', ['reguler', 'promo', 'couple', 'promo couple']); // <--- Pembeda Utama
            $table->string('durasi'); // "1 Bulan", "1 Tahun"
            $table->decimal('harga', 10, 2);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('campaign_id',)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_members');
    }
};
