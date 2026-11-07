<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('order_blocks', function (Blueprint $table) {
            $table->id('block_id');
            $table->string('block_hash')->unique();
            $table->string('previous_hash')->nullable(); // Null untuk Genesis Block (Block Pertama)
            $table->json('order_ids'); // Menyimpan array ID pesanan dalam batch ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_blocks');
    }
};
