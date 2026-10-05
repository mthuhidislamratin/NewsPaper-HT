@extends('frontend.layout')

@section('title', \App\Models\SiteSetting::getValue('seo_home_meta_title', \App\Models\SiteSetting::getValue('seo_site_meta_title', config('jago24.name'))))
@section('description', \App\Models\SiteSetting::getValue('seo_home_meta_description', \App\Models\SiteSetting::getValue('seo_site_meta_description', config('jago24.description'))))
@section('canonical', \App\Models\SiteSetting::getValue('seo_home_canonical_url', route('home')))

@section('content')
    @if($breaking->isNotEmpty())
        <section class="breaking-bar">
            <div class="breaking-label"><span class="sr-only">ব্রেকিং নিউজ</span>ব্রেকিং নিউজ</div>
            <div class="breaking-track">
                @foreach($breaking as $article)
                    <a href="{{ route('article.show', $article) }}">{{ $article->title }}</a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="hero-grid">
        @if($featured->isNotEmpty())
            @php $first = $featured->first(); @endphp
            <article @class(['hero-story', 'card', 'hero-story-no-image' => !$first->getFirstMediaUrl('featured')])>
                @if($first->getFirstMediaUrl('featured'))
                    <a href="{{ route('article.show', $first) }}">
                        <img src="{{ $first->getFirstMediaUrl('featured') }}" alt="{{ $first->getFirstMedia('featured')?->getCustomProperty('alt_text') ?: $first->title }}">
                    </a>
                @endif
                <div class="story-body">
                    <span class="eyebrow">{{ $first->category?->display_name }}</span>
                    <h1><a href="{{ route('article.show', $first) }}">{{ $first->title }}</a></h1>
                    <p>{{ Str::limit($first->excerpt ?: strip_tags($first->content), 160) }}</p>
                </div>
            </article>
            <div class="stacked-stories">
                @foreach($featured->slice(1, 3) as $story)
                    <article @class(['mini-story', 'card', 'mini-story-no-image' => !$story->getFirstMediaUrl('featured')])>
                        @if($story->getFirstMediaUrl('featured'))
                            <a href="{{ route('article.show', $story) }}">
                                <img src="{{ $story->getFirstMediaUrl('featured') }}" alt="{{ $story->getFirstMedia('featured')?->getCustomProperty('alt_text') ?: $story->title }}">
                            </a>
                        @endif
                        <div>
                            <span class="eyebrow">{{ $story->category?->display_name }}</span>
                            <h3><a href="{{ route('article.show', $story) }}">{{ $story->title }}</a></h3>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <article class="hero-story card">
                <div class="brand-hero">
                    <img src="{{ asset(config('jago24.logo')) }}" alt="জাগো২৪বার্তা — সত্যের সন্ধানে আপোষহীন" class="brand-hero-logo">
                    <p>সর্বশেষ সংবাদ, নির্ভরযোগ্য প্রতিবেদন ও প্রয়োজনীয় বিশ্লেষণ পড়ুন।</p>
                </div>
            </article>
            <div class="stacked-stories">
                @foreach($latest->take(2) as $story)
                    <article @class(['mini-story', 'card', 'mini-story-no-image' => !$story->getFirstMediaUrl('featured')])>
                        @if($story->getFirstMediaUrl('featured'))<a href="{{ route('article.show', $story) }}"><img src="{{ $story->getFirstMediaUrl('featured') }}" alt="{{ $story->getFirstMedia('featured')?->getCustomProperty('alt_text') ?: $story->title }}"></a>@endif
                        <div><span class="eyebrow">{{ $story->category?->display_name }}</span><h3><a href="{{ route('article.show', $story) }}">{{ $story->title }}</a></h3></div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="content-grid">
        <div class="main-column">
            <div class="section-header">
                <h2>সর্বশেষ সংবাদ</h2>
            </div>
            <div class="news-list">
                @forelse($latest as $article)
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
                @empty
                    <div class="empty-state panel"><strong>এখনো কোনো সংবাদ প্রকাশিত হয়নি</strong><p>নতুন সংবাদ প্রকাশিত হলে এখানে দেখা যাবে।</p></div>
                @endforelse
            </div>
        </div>

        <aside class="sidebar-column">
            <div class="sidebar-block card">
                <h3>জনপ্রিয় সংবাদ</h3>
                @forelse($trending as $article)
                    <div class="side-link">
                        <a href="{{ route('article.show', $article) }}">{{ $article->title }}</a>
                    </div>
                @empty
                    <p class="muted">পাঠকেরা যে সংবাদগুলো পড়ছেন, তা এখানে দেখুন।</p>
                @endforelse
            </div>

            @if(isset($sidebarAd) && $sidebarAd)
                <div class="sidebar-block card ad-card">
                    <span class="ad-label">বিজ্ঞাপন</span>
                    @if($sidebarAd->image_url && $sidebarAd->target_url)
                        <a href="{{ $sidebarAd->target_url }}" target="_blank" rel="sponsored noopener">
                            <img src="{{ $sidebarAd->image_url }}" alt="{{ $sidebarAd->title }}">
                        </a>
                    @else
                        <p>{{ $sidebarAd->content }}</p>
                    @endif
                </div>
            @endif
        </aside>
    </section>

    @if($categories->contains(fn ($category) => $category->articles->isNotEmpty()))
    <section class="category-grid">
        @foreach($categories->filter(fn ($category) => $category->articles->isNotEmpty()) as $category)
            <div class="card category-card">
                <h3><a href="{{ route('category.show', $category) }}">{{ $category->display_name }}</a></h3>
                @foreach($category->articles as $item)
                    <p><a href="{{ route('article.show', $item) }}">{{ $item->title }}</a></p>
                @endforeach
            </div>
        @endforeach
    </section>
    @endif

    @if($mostRead->isNotEmpty())
        <section class="content-grid editorial-sections">
            <div>
                <div class="section-header"><h2>সর্বাধিক পঠিত</h2></div>
                <ol class="most-read-list">
                    @foreach($mostRead as $article)
                        <li><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><div><span class="eyebrow">{{ $article->category?->display_name }}</span><h3><a href="{{ route('article.show', $article) }}">{{ $article->title }}</a></h3><small>{{ number_format($article->views) }} বার পঠিত</small></div></li>
                    @endforeach
                </ol>
            </div>
            <aside class="newsletter-card card">
                <span class="eyebrow">প্রতিদিনের সংবাদ</span>
                <h2>সত্য ও নির্ভরযোগ্য সংবাদ</h2>
                <p>জাগো২৪বার্তার সর্বশেষ প্রতিবেদন ও গুরুত্বপূর্ণ বিশ্লেষণ পড়ুন।</p>
                <a class="button button-primary" href="{{ route('pages.newsletter') }}">নিউজলেটার</a>
            </aside>
        </section>
    @endif
@endsection
