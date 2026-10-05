<?php

use App\Models\Article;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('news:publish-scheduled', function (): int {
    $articles = Article::query()
        ->where('status', 'scheduled')
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '<=', now())
        ->get();

    foreach ($articles as $article) {
        $article->update([
            'status' => 'published',
            'is_published' => true,
            'published_at' => $article->scheduled_at,
            'scheduled_at' => null,
        ]);
    }

    $this->info($articles->count().' scheduled article(s) published.');

    return self::SUCCESS;
})->purpose('Publish articles when their scheduled publication time arrives');

Schedule::command('news:publish-scheduled')->everyMinute();
