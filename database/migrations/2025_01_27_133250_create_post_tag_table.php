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
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id(); // Egyedi azonosító
            $table->unsignedBigInteger('post_id'); // Kapcsolódó blogbejegyzés
            $table->unsignedBigInteger('tag_id'); // Kapcsolódó címke
        
            // Külső kulcsok
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        
            $table->timestamps(); // Létrehozás és frissítés időbélyeg
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tag');
    }
};
