<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const LEGACY_EMAIL = 'hello@khoborpatra.local';

    public function up(): void
    {
        DB::table('site_settings')
            ->where('key', 'site_email')
            ->where('value', self::LEGACY_EMAIL)
            ->update(['value' => '']);
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->where('key', 'site_email')
            ->where('value', '')
            ->update(['value' => self::LEGACY_EMAIL]);
    }
};
