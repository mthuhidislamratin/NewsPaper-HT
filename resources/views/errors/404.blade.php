@extends('frontend.layout')

@section('title', 'পৃষ্ঠা পাওয়া যায়নি | Jago24Barta')
@section('description', 'আপনি যে সংবাদ বা পৃষ্ঠাটি খুঁজছেন তা পাওয়া যায়নি।')

@section('content')
    <section class="not-found panel">
        <span class="not-found-code">৪০৪</span>
        <h1>এই পৃষ্ঠাটি খুঁজে পাওয়া যায়নি</h1>
        <p>ঠিকানাটি পরীক্ষা করুন, অথবা জাগো২৪বার্তার প্রচ্ছদে ফিরে সর্বশেষ সংবাদ পড়ুন।</p>
        <a class="button button-primary" href="{{ route('home') }}">প্রচ্ছদে ফিরে যান</a>
    </section>
@endsection
