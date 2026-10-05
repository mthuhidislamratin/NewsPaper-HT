@extends('frontend.layout')

@section('title', $query !== '' ? 'অনুসন্ধান: '.$query.' | Jago24Barta' : 'সংবাদ অনুসন্ধান | Jago24Barta')
@section('description', $query !== '' ? $query.' সম্পর্কে জাগো২৪বার্তার সংবাদ ও প্রতিবেদন খুঁজুন।' : 'জাগো২৪বার্তার সংবাদ ও প্রতিবেদন অনুসন্ধান করুন।')
@section('canonical', route('search'))

@section('content')
    <section class="page-header">
        <span class="eyebrow">সংবাদ খুঁজুন</span>
        <h1>অনুসন্ধান</h1>
    </section>
    <form method="GET" action="{{ route('search') }}" class="search-filters panel" role="search">
        <label class="form-span-2">কীওয়ার্ড
            <input type="search" name="q" value="{{ $query }}" maxlength="200" placeholder="শিরোনাম বা সংবাদ খুঁজুন" autofocus>
        </label>
        <label>বিভাগ
            <select name="category_id"><option value="">সব বিভাগ</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->display_name }}</option>@endforeach</select>
        </label>
        <label>প্রতিবেদক
            <select name="author_id"><option value="">সব প্রতিবেদক</option>@foreach($authors as $author)<option value="{{ $author->id }}" @selected(request('author_id') == $author->id)>{{ $author->name }}</option>@endforeach</select>
        </label>
        <label>শুরুর তারিখ <input type="date" name="from" value="{{ request('from') }}"></label>
        <label>শেষের তারিখ <input type="date" name="to" value="{{ request('to') }}"></label>
        <label>সাজান
            <select name="sort"><option value="recent" @selected(request('sort', 'recent') === 'recent')>সর্বশেষ আগে</option><option value="popular" @selected(request('sort') === 'popular')>জনপ্রিয় আগে</option></select>
        </label>
        <div class="search-filter-actions"><button class="button button-primary" type="submit">অনুসন্ধান</button><a class="button button-ghost" href="{{ route('search') }}">মুছুন</a></div>
    </form>

    @if($query === '')
        <div class="empty-state panel search-empty"><strong>সংবাদ বা বিষয় খুঁজুন</strong><p>শিরোনাম বা প্রতিবেদনের লেখা দিয়ে অনুসন্ধান করুন। বিভাগ, প্রতিবেদক ও তারিখ দিয়ে ফলাফল সীমিত করতে পারেন।</p></div>
    @elseif($articles->isEmpty())
        <div class="empty-state panel search-empty"><strong>“{{ $query }}” অনুসন্ধানে কোনো সংবাদ মেলেনি</strong><p>বানান যাচাই করুন, ভিন্ন শব্দ লিখুন অথবা কিছু ফিল্টার সরিয়ে দেখুন।</p><a href="{{ route('search', ['q' => $query]) }}">ফিল্টার মুছুন</a></div>
    @else
        <div class="section-header"><h2>“{{ $query }}” অনুসন্ধানের ফলাফল</h2><span class="muted">{{ $articles->total() }}টি সংবাদ</span></div>
        <div class="news-list">
            @foreach($articles as $article)
                <article @class(['news-item', 'card', 'news-item-no-image' => !$article->getFirstMediaUrl('featured')])>
                    @if($article->getFirstMediaUrl('featured'))<img src="{{ $article->getFirstMediaUrl('featured') }}" alt="{{ $article->getFirstMedia('featured')?->getCustomProperty('alt_text') ?: $article->title }}">@endif
                    <div><div class="meta-row"><span>{{ $article->category?->display_name }}</span><span>{{ $article->published_at?->locale('bn')->translatedFormat('j F Y') }}</span><span>প্রতিবেদক: {{ $article->author?->name ?? 'জাগো২৪বার্তা ডেস্ক' }}</span></div>
                        <h3><a href="{{ route('article.show', $article) }}">{{ $article->title }}</a></h3>
                        <p>{{ Str::limit($article->excerpt ?: strip_tags($article->content), 150) }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        {{ $articles->links() }}
    @endif
@endsection
