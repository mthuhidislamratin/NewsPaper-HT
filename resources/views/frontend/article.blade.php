@extends('frontend.layout')

@section('title', $article->seo_title ?: $article->title.' | Jago24Barta')
@section('og_title', $article->seo_title ?? $article->title)
@section('description', $article->meta_description ?? Str::limit(strip_tags($article->content), 160))
@section('canonical', route('article.show', $article))
@section('og_type', 'article')
@if($article->getFirstMediaUrl('featured'))@section('og_image', $article->getFirstMediaUrl('featured'))@endif

@section('content')
    <article class="article-layout">
        <div class="main-column">
            <div class="article-header">
                <span class="eyebrow">{{ $article->category?->display_name }}</span>
                <h1>{{ $article->title }}</h1>
                <div class="meta-row">
                    <span>প্রতিবেদক: {{ $article->author?->name ?? 'জাগো২৪বার্তা ডেস্ক' }}</span>
                    <span>{{ $article->published_at?->locale('bn')->translatedFormat('j F Y') }}</span>
                    <span>পড়তে {{ max(1, (int) ceil(str_word_count(strip_tags($article->content)) / 220)) }} মিনিট</span>
                </div>
            </div>

            @if($article->getFirstMediaUrl('featured'))
                <figure class="article-figure">
                    <img src="{{ $article->getFirstMediaUrl('featured') }}" alt="{{ $article->getFirstMedia('featured')?->getCustomProperty('alt_text') ?: $article->title }}" class="article-cover">
                    @if($article->getFirstMedia('featured')?->getCustomProperty('caption'))
                        <figcaption>{{ $article->getFirstMedia('featured')->getCustomProperty('caption') }}</figcaption>
                    @endif
                </figure>
            @endif

            <div class="article-body">{{ $article->content }}</div>

            @if($article->tags->isNotEmpty())
                <div class="tag-list">
                    @foreach($article->tags as $tag)
                        <a href="{{ route('tag.show', ['slug' => $tag->slug]) }}">#{{ $tag->display_name }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="sidebar-column">
            @if($ad)
                <div class="sidebar-block card">
                    <span class="ad-label">বিজ্ঞাপন</span>
                    @if($ad->image_url && $ad->target_url)
                        <a href="{{ $ad->target_url }}" target="_blank" rel="sponsored noopener"><img src="{{ $ad->image_url }}" alt="{{ $ad->title }}"></a>
                    @else
                        <p>{{ $ad->content }}</p>
                    @endif
                </div>
            @endif
            <div class="sidebar-block card">
                <h3>আরও সংবাদ</h3>
                @foreach($related as $story)
                    <div class="side-link"><a href="{{ route('article.show', $story) }}">{{ $story->title }}</a></div>
                @endforeach
                @if($related->isEmpty())<p class="muted">এই বিভাগের আরও সংবাদ শিগগিরই যুক্ত হবে।</p>@endif
            </div>
        </aside>
    </article>
    @php
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $article->seo_title ?: $article->title,
            'description' => $article->meta_description ?: Str::limit(strip_tags($article->content), 160),
            'datePublished' => $article->published_at?->toIso8601String(),
            'dateModified' => $article->updated_at?->toIso8601String(),
            'mainEntityOfPage' => route('article.show', $article),
            'author' => ['@type' => 'Person', 'name' => $article->author?->name ?? 'জাগো২৪বার্তা ডেস্ক'],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('jago24.name'),
                'logo' => ['@type' => 'ImageObject', 'url' => asset(config('jago24.logo'))],
            ],
        ];
        if ($article->getFirstMediaUrl('featured')) {
            $articleSchema['image'] = [$article->getFirstMediaUrl('featured')];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection
