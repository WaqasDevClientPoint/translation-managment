<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique(); // en, fr, es, etc.
            $table->string('name', 191);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
}; 