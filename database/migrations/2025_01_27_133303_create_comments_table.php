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
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // Egyedi azonosító
            $table->unsignedBigInteger('post_id'); // Kapcsolódó blogbejegyzés
            $table->unsignedBigInteger('user_id')->nullable(); // Kapcsolódó felhasználó (opcionális)
            $table->text('content'); // Hozzászólás szövege
            $table->enum('status', ['approved', 'pending', 'spam'])->default('pending'); // Státusz
            $table->timestamps(); // Létrehozás és frissítés időbélyeg
        
            // Külső kulcsok
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
