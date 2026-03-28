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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');

            $table->string('member_id', 20)->nullable();

            $table->unsignedBigInteger('kasir_id')->nullable();

            $table->string('invoice_code')->unique();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_payment', 12, 2)->default(0);

            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('paid'); 

            $table->timestamps();

            $table->foreign('member_id')
                ->references('id_members')
                ->on('members')
                ->onDelete('set null');

            $table->foreign('kasir_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
