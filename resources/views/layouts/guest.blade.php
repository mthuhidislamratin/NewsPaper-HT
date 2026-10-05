<!DOCTYPE html>
<html lang="bn">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('jago24.name') }} | নিউজরুমে প্রবেশ</title>
        <meta name="description" content="{{ config('jago24.description') }}">
        <meta property="og:title" content="{{ config('jago24.share_title') }}">
        <meta property="og:image" content="{{ asset(config('jago24.logo')) }}">
        <link rel="icon" type="image/png" href="{{ asset(config('jago24.favicon')) }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-page">
        <main class="auth-shell">
            <div class="auth-brand">
                <a href="{{ route('home') }}" wire:navigate>
                    <x-application-logo />
                </a>
                <p>{{ config('jago24.slogan') }}</p>
            </div>
            <section class="auth-card" aria-label="নিউজরুমে প্রবেশ">
                {{ $slot }}
            </section>
            <a class="auth-home-link" href="{{ route('home') }}">← জাগো২৪বার্তার প্রচ্ছদে ফিরে যান</a>
        </main>
    </body>
</html>
