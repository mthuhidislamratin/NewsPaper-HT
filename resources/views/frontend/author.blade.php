@extends('frontend.layout')

@section('title', $user->name.' | জাগো২৪বার্তা')
@section('description', $user->name.'-এর সর্বশেষ প্রতিবেদন ও সংবাদ জাগো২৪বার্তায়।')
@section('canonical', route('author.show', $user->username ?? $user->name))

@section('content')
    <section class="page-header">
        <span class="eyebrow">প্রতিবেদক</span>
        <h1>{{ $user->name }}</h1>
        <p class="page-description">{{ $articles->total() }}টি প্রকাশিত প্রতিবেদন</p>
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
        <div class="empty-state panel"><strong>এখনো কোনো প্রতিবেদন নেই</strong><p>এই প্রতিবেদকের প্রকাশিত সংবাদ এখানে দেখা যাবে।</p></div>
    @endif
    {{ $articles->links() }}
@endsection
