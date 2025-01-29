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
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Egyedi azonosító
            $table->string('title'); // Bejegyzés címe
            $table->string('slug')->unique(); // URL-barát cím (egyedi)
            $table->text('content'); // Bejegyzés tartalma
            $table->text('excerpt')->nullable(); // Rövid összefoglaló (opcionális)
            $table->foreignId('author_id')->constrained('admins')->onDelete('cascade'); // Hivatkozás az íróra (admins tábla)
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null'); // Hivatkozás a kategóriára (opcionális)
            $table->timestamp('published_at')->nullable(); // Közzététel időpontja
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // Bejegyzés állapota
            $table->timestamps(); // Létrehozás és frissítés időbélyeg
            $table->softDeletes(); // Puha törlés időbélyeg
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
