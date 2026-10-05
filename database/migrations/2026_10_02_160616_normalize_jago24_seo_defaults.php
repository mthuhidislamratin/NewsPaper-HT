<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const LEGACY_DEFAULTS = [
        'seo_home_meta_title' => [
            'KhoborPatra | Home' => 'Jago24Barta',
        ],
        'seo_site_meta_title' => [
            'KhoborPatra' => 'Jago24Barta',
        ],
        'meta_title' => [
            'KhoborPatra' => 'Jago24Barta',
        ],
        'seo_home_meta_description' => [
            'Latest Bangladesh news and analysis.' => 'জাগো২৪বার্তা | সত্যের সন্ধানে আপোষহীন',
        ],
        'seo_site_meta_description' => [
            'Latest Bangladesh news and analysis.' => 'জাগো২৪বার্তা | সত্যের সন্ধানে আপোষহীন',
        ],
        'meta_description' => [
            'Latest Bangladesh news and analysis.' => 'জাগো২৪বার্তা | সত্যের সন্ধানে আপোষহীন',
        ],
    ];

    public function up(): void
    {
        foreach (self::LEGACY_DEFAULTS as $key => $values) {
            foreach ($values as $legacy => $current) {
                DB::table('site_settings')
                    ->where('key', $key)
                    ->where('value', $legacy)
                    ->update(['value' => $current]);
            }
        }
    }

    public function down(): void
    {
        foreach (self::LEGACY_DEFAULTS as $key => $values) {
            foreach ($values as $legacy => $current) {
                DB::table('site_settings')
                    ->where('key', $key)
                    ->where('value', $current)
                    ->update(['value' => $legacy]);
            }
        }
    }
};
