<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="Jago24Barta">
    <meta property="og:site_name" content="Jago24Barta">
    <meta property="og:locale" content="bn_BD">
    <link rel="icon" type="image/png" href="{{ asset(config('jago24.favicon')) }}">
    <link rel="apple-touch-icon" href="{{ asset(config('jago24.logo')) }}">
    <title>@yield('title', \App\Models\SiteSetting::getValue('seo_site_meta_title', config('jago24.name')))</title>
    <meta name="description" content="@yield('description', \App\Models\SiteSetting::getValue('seo_site_meta_description', config('jago24.description')))">
    <link rel="canonical" href="@yield('canonical', request()->url())">
    <meta property="og:title" content="@yield('og_title', config('jago24.share_title'))">
    <meta property="og:description" content="@yield('description', \App\Models\SiteSetting::getValue('seo_site_meta_description', config('jago24.description')))">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical', request()->url())">
    <meta property="og:image" content="@yield('og_image', asset(config('jago24.logo')))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', config('jago24.share_title'))">
    <meta name="twitter:description" content="@yield('description', \App\Models\SiteSetting::getValue('seo_site_meta_description', config('jago24.description')))">
    <meta name="twitter:image" content="@yield('og_image', asset(config('jago24.logo')))">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-theme="light">
    <a class="skip-link" href="#main-content">মূল বিষয়বস্তুতে যান</a>
    <header class="site-header">
        <div class="utility-bar">
            <div class="container utility-inner">
                <span>{{ now()->locale('bn')->translatedFormat('l, j F Y') }}</span>
                <div class="utility-links">
                    <a href="{{ route('home') }}">বাংলাদেশ সংস্করণ</a>
                    <a class="optional-mobile" href="{{ route('pages.contact') }}">যোগাযোগ</a>
                    @auth
                        @if(auth()->user()->can('access-admin'))<a class="optional-mobile" href="{{ route('admin.dashboard') }}">নিউজরুম</a>@endif
                    @endauth
                </div>
            </div>
        </div>
        <div class="container topbar">
            <div class="brand-wrap">
                <a href="{{ route('home') }}" class="brand-logo-link" aria-label="জাগো২৪বার্তা — প্রচ্ছদ">
                    <img class="brand-logo" src="{{ asset(config('jago24.logo')) }}" alt="জাগো২৪বার্তা">
                </a>
                <p class="masthead-tagline">{{ config('jago24.slogan') }}</p>
            </div>
            <div class="masthead-actions">
                <form action="{{ route('search') }}" method="GET" class="search-box" role="search">
                    <label class="sr-only" for="site-search">সংবাদ অনুসন্ধান</label>
                    <input id="site-search" type="search" name="q" placeholder="সংবাদ খুঁজুন" value="{{ request('q') }}">
                    <button type="submit">অনুসন্ধান</button>
                </form>
                <a class="button button-ghost mobile-search" href="{{ route('search') }}" aria-label="অনুসন্ধান">⌕</a>
                <button type="button" class="theme-toggle" aria-label="রাতের থিম চালু বা বন্ধ করুন" data-theme-toggle>◐</button>
                <button type="button" class="menu-toggle" aria-expanded="false" aria-controls="public-navigation" data-menu-toggle aria-label="মেনু খুলুন">☰</button>
            </div>
        </div>
        <nav class="main-nav" id="public-navigation" aria-label="প্রধান নেভিগেশন">
            <div class="container nav-wrap">
                <a href="{{ route('home') }}">প্রচ্ছদ</a>
                <a href="{{ route('search') }}">অনুসন্ধান</a>
                @foreach(\App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->limit(8)->get() as $category)
                    <a href="{{ route('category.show', $category) }}">{{ $category->display_name }}</a>
                @endforeach
                <a href="{{ route('pages.about') }}">আমাদের সম্পর্কে</a>
            </div>
        </nav>
    </header>

    @if(isset($topAd) && $topAd)
        <div class="container ad-wrap" aria-label="বিজ্ঞাপন">
            <span class="ad-label">বিজ্ঞাপন</span>
            @if($topAd->image_url && $topAd->target_url)
                <a href="{{ $topAd->target_url }}" target="_blank" rel="sponsored noopener">
                    <img src="{{ $topAd->image_url }}" alt="{{ $topAd->title }}">
                </a>
            @else
                <p>{{ $topAd->content }}</p>
            @endif
        </div>
    @endif

    <main class="container main-content" id="main-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="footer-brand" href="{{ route('home') }}"><img src="{{ asset(config('jago24.logo')) }}" alt="জাগো২৪বার্তা"></a>
                <p>{{ config('jago24.slogan') }}</p>
            </div>
            <div>
                <h4>বিভাগসমূহ</h4>
                <ul>
                    @foreach(\App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->limit(5)->get() as $category)
                        <li><a href="{{ route('category.show', $category) }}">{{ $category->display_name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4>জাগো২৪বার্তা</h4>
                <ul><li><a href="{{ route('pages.about') }}">আমাদের সম্পর্কে</a></li><li><a href="{{ route('pages.contact') }}">যোগাযোগ</a></li></ul>
            </div>
            <div><h4>পাঠক সহায়তা</h4><p>সংবাদ, মতামত বা সংশোধনী জানাতে আমাদের সঙ্গে যোগাযোগ করুন।</p><a href="{{ route('pages.contact') }}">যোগাযোগের ঠিকানা ও ফর্ম</a></div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ now()->year }} জাগো২৪বার্তা। সর্বস্বত্ব সংরক্ষিত।</span>
        </div>
    </footer>
</body>
</html>
