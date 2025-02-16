<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->unique();
            $table->timestamps();
        });

        Schema::create('translation_tag', function (Blueprint $table) {
            $table->foreignId('translation_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            
            $table->primary(['translation_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_tag');
        Schema::dropIfExists('tags');
    }
}; 