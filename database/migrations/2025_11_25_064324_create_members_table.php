<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->string('id_members', 20)->primary();
            $table->string('nama_lengkap');
            $table->string('alamat');
            $table->string('no_telepon');
            $table->string('email')->unique();
            $table->string('partner_id', 20)->nullable();
            $table->string('jenis_kelamin');
            $table->date('tanggal_daftar');
            $table->date('tanggal_selesai');
            $table->string('status');
            $table->string('level')->default('intermediate');
            $table->boolean('is_opt_out')->default(false);
            $table->timestamps();

            $table->foreignId('paket_id')
                ->nullable()
                ->constrained('paket_members', 'id_paket')
                ->onDelete('set null');

            $table->foreign('partner_id')
                ->references('id_members')
                ->on('members')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
