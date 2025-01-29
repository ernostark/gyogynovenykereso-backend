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
        Schema::create('media', function (Blueprint $table) {
            $table->id(); // Egyedi azonosító
            $table->unsignedBigInteger('post_id')->nullable(); // Kapcsolódó blogbejegyzés (opcionális)
            $table->string('url'); // Médiafájl URL-je
            $table->string('type'); // Fájl típusa (pl. image, video, stb.)
            $table->timestamps(); // Létrehozás és frissítés időbélyeg
        
            // Külső kulcs
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
