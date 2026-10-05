@extends('frontend.layout')

@section('title', 'আমাদের সম্পর্কে | Jago24Barta')
@section('description', 'জাগো২৪বার্তা — সত্যের সন্ধানে আপোষহীন। আমাদের সংবাদনীতি ও পাঠকসেবার কথা জানুন।')
@section('canonical', route('pages.about'))

@section('content')
    <article class="editorial-page">
        <header class="page-header">
            <span class="eyebrow">আমাদের পরিচয়</span>
            <h1>জাগো২৪বার্তা</h1>
            <p class="page-description">সত্যের সন্ধানে আপোষহীন</p>
        </header>
        <section class="editorial-copy">
            <h2>সংবাদের প্রতি দায়বদ্ধতা</h2>
            <p>জাগো২৪বার্তা বাংলাদেশের পাঠকদের জন্য সংবাদ, প্রতিবেদন ও প্রাসঙ্গিক তথ্য তুলে ধরার একটি বাংলা ডিজিটাল সংবাদমাধ্যম।</p>
            <p>তথ্য যাচাই, নিরপেক্ষতা, স্পষ্ট ভাষা এবং পাঠকের প্রতি দায়বদ্ধতা আমাদের সম্পাদকীয় কাজের ভিত্তি। সংবাদে ভুল চোখে পড়লে সংশোধনের জন্য আমাদের জানান।</p>
            <a class="button button-primary" href="{{ route('pages.contact') }}">যোগাযোগ করুন</a>
        </section>
    </article>
@endsection
