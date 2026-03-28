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
        Schema::create('fingerprints', function (Blueprint $table) {
            $table->id();

            $table->string('member_id', 20)->nullable();

            $table->text('fingerprint_template')->nullable(); 

            $table->string('device_id')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            // 3. Foreign key sudah BENAR (String ke String)
            $table->foreign('member_id')
                  ->references('id_members')
                  ->on('members')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fingerprints');
    }
};