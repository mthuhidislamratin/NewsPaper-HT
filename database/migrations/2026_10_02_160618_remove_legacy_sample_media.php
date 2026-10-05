<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class extends Migration
{
    public function up(): void
    {
        $fixtures = Media::query()
            ->where('name', 'khoborpatra-sample')
            ->where('file_name', 'khoborpatra-sample.png')
            ->whereJsonContains('custom_properties->caption', 'Sample media asset for validation.')
            ->get();

        foreach ($fixtures as $fixture) {
            $fixture->delete();
        }
    }

    public function down(): void
    {
    }
};
