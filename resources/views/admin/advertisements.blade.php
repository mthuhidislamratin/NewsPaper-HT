@extends('admin.layout')

@section('title', 'Advertisements')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Revenue operations</p><h1>Advertisements</h1><p class="page-description">Manage safe, scheduled creatives across reserved placements.</p></div></div>
    <section class="panel">
        <h2>Create advertisement</h2>
        <form method="POST" action="{{ route('admin.advertisements.store') }}" class="form-grid form-grid-wide">
            @csrf
            <label>Internal name <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"></label>
            <label>Placement
                <select name="placement" required>
                    @foreach(['header' => 'Top banner', 'sidebar' => 'Sidebar', 'content_top' => 'Article top', 'in_article' => 'In article', 'footer' => 'Footer', 'mobile_sticky' => 'Mobile sticky'] as $value => $label)<option value="{{ $value }}" @selected(old('placement') === $value)>{{ $label }}</option>@endforeach
                </select>
            </label>
            <label>Public label <input type="text" name="title" value="{{ old('title') }}" maxlength="255" placeholder="Advertisement"></label>
            <label>Image URL <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://…"></label>
            <label>Destination URL <input type="url" name="target_url" value="{{ old('target_url') }}" placeholder="https://…"></label>
            <label>Start time <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"></label>
            <label>End time <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"></label>
            <label>Sort priority <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"></label>
            <label class="form-span-2">Copy <textarea name="content" rows="2" maxlength="2000">{{ old('content') }}</textarea></label>
            <label class="check-list form-span-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active</label>
            <div class="form-span-2"><button type="submit" class="button button-primary">Create advertisement</button></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>Campaign creatives</h2><span class="muted">{{ $advertisements->count() }} ads</span></div></div>
        <div class="table-scroll">
            <table class="table-list">
                <thead><tr><th>Name</th><th>Placement</th><th>Flight</th><th>Status</th><th>Manage</th></tr></thead>
                <tbody>
                    @forelse($advertisements as $ad)
                        <tr>
                            <td><strong>{{ $ad->name }}</strong><small class="table-subtitle">{{ $ad->title }}</small></td>
                            <td>{{ str($ad->placement)->replace('_', ' ')->title() }}</td>
                            <td>{{ $ad->starts_at?->format('M j, Y') ?? 'No start' }} – {{ $ad->ends_at?->format('M j, Y') ?? 'No end' }}</td>
                            <td><span class="status-badge {{ $ad->is_active ? 'status-active' : 'status-inactive' }}">{{ $ad->is_active ? 'Active' : 'Paused' }}</span></td>
                            <td class="row-actions">
                                <details class="row-edit"><summary>Edit</summary>
                                    <form method="POST" action="{{ route('admin.advertisements.update', $ad) }}" class="form-grid row-edit-form">
                                        @csrf @method('PUT')
                                        <label>Name <input name="name" value="{{ $ad->name }}" required></label>
                                        <label>Placement <select name="placement">@foreach(['header','sidebar','content_top','in_article','footer','mobile_sticky'] as $placement)<option value="{{ $placement }}" @selected($ad->placement === $placement)>{{ str($placement)->replace('_', ' ')->title() }}</option>@endforeach</select></label>
                                        <label>Label <input name="title" value="{{ $ad->title }}"></label>
                                        <label>Image URL <input type="url" name="image_url" value="{{ $ad->image_url }}"></label>
                                        <label>Destination URL <input type="url" name="target_url" value="{{ $ad->target_url }}"></label>
                                        <label>Start <input type="datetime-local" name="starts_at" value="{{ $ad->starts_at?->format('Y-m-d\TH:i') }}"></label>
                                        <label>End <input type="datetime-local" name="ends_at" value="{{ $ad->ends_at?->format('Y-m-d\TH:i') }}"></label>
                                        <label>Copy <textarea name="content">{{ $ad->content }}</textarea></label>
                                        <label class="check-list"><input type="checkbox" name="is_active" value="1" @checked($ad->is_active)> Active</label>
                                        <button class="button button-primary" type="submit">Save ad</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.advertisements.delete', $ad) }}" onsubmit="return confirm('Archive this ad?')">@csrf @method('DELETE')<button class="link-button" type="submit">Archive</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state"><strong>No ads yet</strong><p>Create a creative and choose its placement.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
