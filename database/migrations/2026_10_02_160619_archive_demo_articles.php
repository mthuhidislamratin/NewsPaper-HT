<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const DEMO_ARTICLES = [
        'new-rail-link-to-boost-connectivity' => 'New rail link to boost connectivity',
        'city-park-renovation-starts' => 'City park renovation starts',
        'ai-chip-race-intensifies' => 'AI chip race intensifies',
    ];

    public function up(): void
    {
        foreach (self::DEMO_ARTICLES as $slug => $title) {
            DB::table('articles')
                ->where('slug', $slug)
                ->where('title', $title)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        foreach (self::DEMO_ARTICLES as $slug => $title) {
            DB::table('articles')
                ->where('slug', $slug)
                ->where('title', $title)
                ->whereNotNull('deleted_at')
                ->update(['deleted_at' => null, 'updated_at' => now()]);
        }
    }
};
