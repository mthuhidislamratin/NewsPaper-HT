@extends('admin.layout')

@section('title', 'Audit log')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Security and accountability</p><h1>Audit log</h1><p class="page-description">Search a redacted record of changes made by newsroom users.</p></div></div>
    <form method="GET" action="{{ route('admin.audit') }}" class="filter-bar panel">
        <label class="filter-search">Search actor, action or record <input type="search" name="q" value="{{ request('q') }}" placeholder="Search audit events"></label>
        <label>Action <select name="action"><option value="">All actions</option>@foreach($actions as $action)<option value="{{ $action }}" @selected(request('action') === $action)>{{ str($action)->replace('.', ' · ')->replace('_', ' ')->title() }}</option>@endforeach</select></label>
        <button class="button button-secondary" type="submit">Filter</button>
        <a class="button button-ghost" href="{{ route('admin.audit') }}">Clear</a>
    </form>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>Recorded activity</h2><span class="muted">{{ $events->total() }} events</span></div></div>
        <div class="table-scroll"><table class="table-list">
            <thead><tr><th>Actor</th><th>Action</th><th>Target</th><th>Network</th><th>Changes</th><th>When</th></tr></thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td>{{ $event->actor_name ?? 'Deleted account' }}</td>
                        <td><strong>{{ str($event->action)->replace('.', ' · ')->replace('_', ' ')->title() }}</strong></td>
                        <td>{{ $event->subject_type }} #{{ $event->subject_id ?? '—' }}</td>
                        <td>{{ $event->ip_address ?? '—' }}</td>
                        <td><details><summary>View redacted fields</summary><pre class="audit-json">{{ json_encode(json_decode($event->changes, true) ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></details></td>
                        <td>{{ \Carbon\Carbon::parse($event->created_at)->format('M j, Y g:i:s a') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><strong>No activity recorded</strong><p>Content and access changes will appear here.</p></div></td></tr>
                @endforelse
            </tbody>
        </table></div>
        <div class="pagination-wrap">{{ $events->links() }}</div>
    </section>
@endsection
