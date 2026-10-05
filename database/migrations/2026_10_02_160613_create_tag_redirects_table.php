<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_slug')->unique();
            $table->foreignId('target_tag_id')->constrained('tags')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_redirects');
    }
};
