@extends('frontend.layout')

@section('title', 'নিউজলেটার | Jago24Barta')
@section('description', 'জাগো২৪বার্তার সাম্প্রতিক সংবাদ ও নির্বাচিত প্রতিবেদন ইমেইলে পেতে যোগাযোগ করুন।')
@section('canonical', route('pages.newsletter'))

@section('content')
    <article class="newsletter-page card">
        <span class="eyebrow">জাগো২৪বার্তার পাঠক</span>
        <h1>সংবাদের সঙ্গে থাকুন</h1>
        <p>নির্বাচিত প্রতিবেদন ও গুরুত্বপূর্ণ সংবাদ ইমেইলে পেতে আপনার নাম ও ইমেইল ঠিকানাসহ আমাদের লিখুন।</p>
        @if($email = \App\Models\SiteSetting::getValue('site_email'))
            <a class="button button-primary" href="mailto:{{ $email }}?subject={{ rawurlencode('জাগো২৪বার্তা নিউজলেটার') }}">নিউজলেটারের জন্য ইমেইল করুন</a>
        @else
            <p class="muted">নিউজলেটার সেবার যোগাযোগের ঠিকানা বর্তমানে প্রকাশ করা হয়নি।</p>
        @endif
    </article>
@endsection
