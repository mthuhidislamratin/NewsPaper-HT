<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'নিউজরুম') · Jago24Barta</title>
    <meta name="application-name" content="Jago24Barta">
    <meta property="og:title" content="Jago24Barta | সত্যের সন্ধানে আপোষহীন">
    <link rel="icon" type="image/png" href="{{ asset(config('jago24.favicon')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-theme="light">
    <a class="skip-link" href="#admin-main">মূল বিষয়বস্তুতে যান</a>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}"><img class="brand-logo" src="{{ asset(config('jago24.logo')) }}" alt="জাগো২৪বার্তা"><small>সম্পাদকীয় ব্যবস্থাপনা</small></a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="admin-navigation" data-menu-toggle>মেনু</button>
            <nav class="admin-nav" id="admin-navigation" aria-label="নিউজরুম নেভিগেশন">
                <span class="nav-group-label">কর্মক্ষেত্র</span>
                <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>ড্যাশবোর্ড</a>
                <a href="{{ route('admin.articles') }}" @if(request()->routeIs('admin.articles*')) aria-current="page" @endif>সংবাদ</a>
                <a href="{{ route('admin.categories') }}" @if(request()->routeIs('admin.categories*')) aria-current="page" @endif>বিভাগ</a>
                <a href="{{ route('admin.tags') }}" @if(request()->routeIs('admin.tags*')) aria-current="page" @endif>ট্যাগ</a>
                <span class="nav-group-label">প্রকাশনা</span>
                <a href="{{ route('admin.media') }}" @if(request()->routeIs('admin.media*')) aria-current="page" @endif>মিডিয়া লাইব্রেরি</a>
                <a href="{{ route('admin.advertisements') }}" @if(request()->routeIs('admin.advertisements*')) aria-current="page" @endif>বিজ্ঞাপন</a>
                <a href="{{ route('admin.homepage') }}" @if(request()->routeIs('admin.homepage*')) aria-current="page" @endif>প্রচ্ছদ সাজান</a>
                <a href="{{ route('admin.seo') }}" @if(request()->routeIs('admin.seo*')) aria-current="page" @endif>এসইও ব্যবস্থাপনা</a>
                <span class="nav-group-label">প্রশাসন</span>
                <a href="{{ route('admin.users') }}" @if(request()->routeIs('admin.users*')) aria-current="page" @endif>ব্যবহারকারী</a>
                <a href="{{ route('admin.roles') }}" @if(request()->routeIs('admin.roles*')) aria-current="page" @endif>ভূমিকা</a>
                <a href="{{ route('admin.permissions') }}" @if(request()->routeIs('admin.permissions*')) aria-current="page" @endif>অনুমতি</a>
                @can('view_audit')<a href="{{ route('admin.audit') }}" @if(request()->routeIs('admin.audit*')) aria-current="page" @endif>কার্যক্রমের নথি</a>@endcan
                <a href="{{ route('admin.settings') }}" @if(request()->routeIs('admin.settings*')) aria-current="page" @endif>সেটিংস</a>
                <span class="nav-group-label">ওয়েবসাইট</span>
                <a href="{{ route('home') }}">প্রচ্ছদ দেখুন</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">লগআউট</button>
                </form>
            </nav>
        </aside>
        <main class="admin-main" id="admin-main">
            <header class="admin-topbar">
                <div class="breadcrumb">নিউজরুম <span aria-hidden="true">/</span> @yield('title', 'ড্যাশবোর্ড')</div>
                <div class="masthead-actions">
                    <span class="admin-user">{{ auth()->user()->name }}</span>
                    <button type="button" class="theme-toggle" aria-label="Toggle dark mode" data-theme-toggle>◐</button>
                </div>
            </header>
            @if(session('success'))
                <div class="flash-message" role="status">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-message flash-error" role="alert">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="flash-message flash-error" role="alert">
                    <strong>অনুগ্রহ করে নিচের তথ্যগুলো যাচাই করুন:</strong>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
