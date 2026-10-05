@extends('frontend.layout')

@section('title', 'যোগাযোগ | Jago24Barta')
@section('description', 'জাগো২৪বার্তার সম্পাদকীয় ডেস্কের সঙ্গে যোগাযোগ করুন।')
@section('canonical', route('pages.contact'))

@section('content')
    <article class="editorial-page">
        <header class="page-header">
            <span class="eyebrow">পাঠকসেবা</span>
            <h1>যোগাযোগ</h1>
            <p class="page-description">সংবাদ, সংশোধনী, মতামত বা বিজ্ঞাপন বিষয়ে আমাদের লিখুন।</p>
        </header>
        <section class="contact-options">
            <div class="panel">
                <h2>সম্পাদকীয় ডেস্ক</h2>
                <p>সংবাদ বা তথ্য পাঠাতে বিষয়সহ ইমেইল করুন। প্রকাশের আগে পাঠানো তথ্য যাচাই করা হবে।</p>
                @if($email = \App\Models\SiteSetting::getValue('site_email'))
                    <a class="button button-primary" href="mailto:{{ $email }}">{{ $email }}</a>
                @else
                    <p class="muted">যোগাযোগের ইমেইল ঠিকানা বর্তমানে প্রকাশ করা হয়নি।</p>
                @endif
            </div>
            <div class="panel">
                <h2>বিজ্ঞাপন ও অংশীদারত্ব</h2>
                <p>বিজ্ঞাপন সংক্রান্ত প্রস্তাবে প্রতিষ্ঠানের পরিচিতি ও যোগাযোগের তথ্য যুক্ত করুন।</p>
                @if($email)
                    <a href="mailto:{{ $email }}?subject={{ rawurlencode('বিজ্ঞাপন ও অংশীদারত্ব') }}">বিজ্ঞাপন বিষয়ে ইমেইল পাঠান</a>
                @else
                    <span class="muted">যোগাযোগের ঠিকানা শিগগিরই এখানে যুক্ত হবে।</span>
                @endif
            </div>
        </section>
    </article>
@endsection
