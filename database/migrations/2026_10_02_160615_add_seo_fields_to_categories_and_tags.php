<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('canonical_url', 2048)->nullable();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('canonical_url', 2048)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'meta_description', 'canonical_url']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'meta_description', 'canonical_url']);
        });
    }
};
