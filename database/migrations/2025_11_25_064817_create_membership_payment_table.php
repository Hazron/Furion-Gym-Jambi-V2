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
        Schema::create('membership_payment', function (Blueprint $table) {
            $table->id();
            $table->string('member_id', 20);
            $table->unsignedBigInteger('paket_id');
            $table->string('jenis_transaksi');
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal_transaksi');
            $table->string('metode_pembayaran')->nullable();
            $table->decimal('nominal', 10, 2);
            $table->string('status_pembayaran'); 
            $table->string('admin_id'); 
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('member_id')->references('id_members')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_payment');
    }
};
