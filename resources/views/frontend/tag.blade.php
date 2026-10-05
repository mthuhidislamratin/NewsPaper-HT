@extends('frontend.layout')

@section('title', $tag->seo_title ?: '#'.$tag->display_name.' | Jago24Barta')
@section('description', $tag->meta_description ?: '#'.$tag->display_name.' বিষয়ক সর্বশেষ সংবাদ ও প্রতিবেদন জাগো২৪বার্তায়।')
@section('canonical', $tag->canonical_url ?: route('tag.show', ['slug' => $tag->slug]))

@section('content')
    <section class="page-header">
        <span class="eyebrow">বিষয়ভিত্তিক সংবাদ</span>
        <h1>#{{ $tag->display_name }}</h1>
    </section>

    <div class="news-list">
        @foreach($articles as $article)
            <article @class(['news-item', 'card', 'news-item-no-image' => !$article->getFirstMediaUrl('featured')])>
            @if($article->getFirstMediaUrl('featured'))<img src="{{ $article->getFirstMediaUrl('featured') }}" alt="{{ $article->getFirstMedia('featured')?->getCustomProperty('alt_text') ?: $article->title }}">@endif
                <div>
                    <div class="meta-row">
                        <span>{{ $article->category?->display_name }}</span>
                        <span>{{ $article->published_at?->locale('bn')->translatedFormat('j F Y') }}</span>
                    </div>
                    <h3><a href="{{ route('article.show', $article) }}">{{ $article->title }}</a></h3>
                    <p>{{ Str::limit($article->excerpt ?: strip_tags($article->content), 140) }}</p>
                </div>
            </article>
        @endforeach
    </div>
    @if($articles->isEmpty())
        <div class="empty-state panel"><strong>এই বিষয়ে এখনো কোনো সংবাদ নেই</strong><p>অন্য ট্যাগ বা বিভাগ থেকে সংবাদ দেখুন।</p></div>
    @endif
    {{ $articles->links() }}
@endsection
