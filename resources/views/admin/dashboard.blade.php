@extends('admin.layout')

@section('title', 'Overview')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Friday, {{ now()->format('F j, Y') }}</p><h1>Newsroom overview</h1><p class="page-description">A live snapshot of your publishing desk.</p></div>
        <a href="{{ route('admin.articles') }}#new-article" class="button button-primary">Write a story</a>
    </div>
    <div class="stat-grid">
        @php
            $statusByLabel = ['Published' => 'published', 'Drafts' => 'draft', 'Scheduled' => 'scheduled'];
        @endphp
        @foreach([
            ['All stories', $stats['articles'], ''],
            ['Published', $stats['published'], 'status-published'],
            ['Drafts', $stats['drafts'], 'status-draft'],
            ['Scheduled', $stats['scheduled'], 'status-scheduled'],
            ['Categories', $stats['categories'], ''],
            ['Topics', $stats['tags'], ''],
            ['Media assets', $stats['media'], ''],
            ['Ad creatives', $stats['ads'], ''],
        ] as [$label, $value, $status])
            @php
                $href = isset($statusByLabel[$label])
                    ? route('admin.articles', ['status' => $statusByLabel[$label]])
                    : match ($label) {
                        'All stories' => route('admin.articles'),
                        'Categories' => route('admin.categories'),
                        'Topics' => route('admin.tags'),
                        'Media assets' => route('admin.media'),
                        default => route('admin.advertisements'),
                    };
            @endphp
            <a class="stat-card stat-card-link" href="{{ $href }}">
                <strong>{{ $label }}</strong><span class="stat-value">{{ number_format($value) }}</span>
            </a>
        @endforeach
    </div>
    <section class="panel table-panel dashboard-recent">
        <div class="table-toolbar"><div><h2>Recently updated stories</h2><span class="muted">Latest newsroom activity</span></div><a href="{{ route('admin.articles') }}">View all</a></div>
        <div class="table-scroll"><table class="table-list">
            <thead><tr><th>Headline</th><th>Status</th><th>Section</th><th>By</th><th>Updated</th><th></th></tr></thead>
            <tbody>
                @forelse($recentArticles as $article)
                    <tr><td><strong>{{ $article->title }}</strong></td><td><span class="status-badge status-{{ $article->status }}">{{ str($article->status)->replace('_', ' ')->title() }}</span></td><td>{{ $article->category?->name ?? 'Unassigned' }}</td><td>{{ $article->author?->name ?? 'Staff' }}</td><td>{{ $article->updated_at->diffForHumans() }}</td><td><a href="{{ route('admin.articles.edit', $article) }}">Open</a></td></tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><strong>The desk is quiet</strong><p>Start a story to begin building your homepage.</p><a href="{{ route('admin.articles') }}#new-article">Write an article</a></div></td></tr>
                @endforelse
            </tbody>
        </table></div>
    </section>
@endsection
