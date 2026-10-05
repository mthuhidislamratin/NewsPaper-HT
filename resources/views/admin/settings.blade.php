@extends('admin.layout')

@section('title', 'Settings')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Configuration</p><h1>Site settings</h1><p class="page-description">Manage public identity and default publishing metadata.</p></div></div>
    <form method="POST" action="{{ route('admin.settings.store') }}" class="panel form-grid form-grid-wide">
        @csrf
        <div class="form-span-2"><h2>সাইট পরিচিতি</h2></div>
        <label>ব্র্যান্ড নাম <input type="text" value="{{ config('jago24.name') }} ({{ config('jago24.name_bn') }})" readonly aria-describedby="brand-lock-hint"></label>
        <label>অফিশিয়াল স্লোগান <input type="text" value="{{ config('jago24.slogan') }}" readonly aria-describedby="brand-lock-hint"></label>
        <p id="brand-lock-hint" class="field-hint form-span-2">অফিশিয়াল নাম, স্লোগান ও লোগো ব্র্যান্ড নির্দেশিকা অনুযায়ী স্থির রাখা হয়েছে।</p>
        <label>Newsroom email <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email'] ?? '') }}"></label>
        <label>Default timezone <input type="text" name="default_timezone" value="{{ old('default_timezone', $settings['default_timezone'] ?? 'Asia/Dhaka') }}" required placeholder="Asia/Dhaka"></label>
        <div class="form-span-2"><h2>সার্চ ও সামাজিক যোগাযোগের ডিফল্ট</h2></div>
        <label>ডিফল্ট মেটা শিরোনাম <input type="text" name="meta_title" value="{{ old('meta_title', $settings['meta_title'] ?? config('jago24.name')) }}" maxlength="255"></label>
        <label>ডিফল্ট মেটা বিবরণ <textarea name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $settings['meta_description'] ?? config('jago24.description')) }}</textarea></label>
        <div class="form-span-2"><p class="field-hint">Credentials and API secrets are intentionally managed outside this public settings form.</p></div>
        <div class="form-span-2"><button type="submit" class="button button-primary">Save settings</button></div>
    </form>
@endsection
