<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->index();
            $table->text('value');
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Add a composite index for faster lookups
            $table->unique(['key', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
}; 