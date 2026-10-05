@extends('admin.layout')

@section('title', 'SEO manager')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Search visibility</p><h1>SEO manager</h1><p class="page-description">Review default metadata and the homepage search preview.</p></div></div>
    <section class="panel">
        <h2>Homepage search and social preview</h2>
        <div class="seo-preview"><span>https://{{ request()->getHost() }}/</span><strong>{{ $settings['seo_home_meta_title'] ?? $settings['meta_title'] ?? config('jago24.name') }}</strong><p>{{ $settings['seo_home_meta_description'] ?? $settings['meta_description'] ?? config('jago24.description') }}</p></div>
        <form method="POST" action="{{ route('admin.seo.store') }}" class="form-grid form-grid-wide">
            @csrf
            <input type="hidden" name="page" value="home">
            <label>Meta title <input type="text" name="meta_title" value="{{ old('meta_title', $settings['seo_home_meta_title'] ?? '') }}" maxlength="255"></label>
            <label>Canonical URL <input type="url" name="canonical_url" value="{{ old('canonical_url', $settings['seo_home_canonical_url'] ?? '') }}" placeholder="{{ route('home') }}"></label>
            <label class="form-span-2">Meta description <textarea name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $settings['seo_home_meta_description'] ?? '') }}</textarea></label>
            <div class="form-span-2"><button class="button button-primary" type="submit">Save homepage SEO</button></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>Article metadata coverage</h2><span class="muted">Recent articles with missing fields are surfaced first.</span></div></div>
        <div class="table-scroll"><table class="table-list">
            <thead><tr><th>Article</th><th>SEO title</th><th>Description</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($articles as $article)
                    <tr><td><a href="{{ route('admin.articles.edit', $article) }}">{{ $article->title }}</a></td><td>{{ $article->seo_title ? 'Complete' : 'Uses headline' }}</td><td>{{ $article->meta_description ? 'Complete' : 'Generated from story' }}</td><td><span class="status-badge status-{{ $article->status }}">{{ str($article->status)->replace('_', ' ')->title() }}</span></td></tr>
                @empty
                    <tr><td colspan="4"><div class="empty-state"><strong>No stories to audit</strong><p>Articles will appear here after they are created.</p></div></td></tr>
                @endforelse
            </tbody>
        </table></div>
    </section>
@endsection
