@extends('frontend.layout')

@section('title', $category->seo_title ?: $category->display_name.' | Jago24Barta')
@section('description', $category->meta_description ?: 'জাগো২৪বার্তায় '.$category->display_name.' বিভাগের সর্বশেষ সংবাদ ও বিশ্লেষণ।')
@section('canonical', $category->canonical_url ?: route('category.show', $category))

@section('content')
    <section class="category-page">
        <div class="page-header">
            <span class="eyebrow">বিভাগ</span>
            <h1>{{ $category->display_name }}</h1>
        </div>

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
                        <p>{{ Str::limit($article->excerpt ?: strip_tags($article->content), 150) }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        @if($articles->isEmpty())
            <div class="empty-state panel"><strong>এখনো কোনো সংবাদ প্রকাশিত হয়নি</strong><p>এই বিভাগে নতুন সংবাদ প্রকাশিত হলে এখানে দেখা যাবে।</p></div>
        @endif

        {{ $articles->links() }}
    </section>
@endsection
