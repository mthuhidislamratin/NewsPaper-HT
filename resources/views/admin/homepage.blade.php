@extends('admin.layout')

@section('title', 'Homepage builder')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Presentation</p><h1>Homepage builder</h1><p class="page-description">Choose which editorial sections appear and set their order and story count.</p></div><a class="button button-secondary" href="{{ route('home') }}" target="_blank" rel="noopener">Preview homepage</a></div>
    <section class="panel">
        <h2>Add a section</h2>
        <form method="POST" action="{{ route('admin.homepage.store') }}" class="form-grid form-grid-wide">
            @csrf
            <label>Internal key <input type="text" name="key" value="{{ old('key') }}" placeholder="editors-picks" maxlength="100" required></label>
            <label>Section title <input type="text" name="title" value="{{ old('title') }}" maxlength="255" required></label>
            <label>Stories to show <input type="number" name="count" value="{{ old('count', 5) }}" min="1" max="50" required></label>
            <label>Position <input type="number" name="sort_order" value="{{ old('sort_order', $homepageSections->count() + 1) }}" min="0" required></label>
            <label class="check-list form-span-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Visible on homepage</label>
            <div class="form-span-2"><button class="button button-primary" type="submit">Add section</button></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>Homepage sections</h2><span class="muted">Lower position numbers appear first.</span></div></div>
        <div class="table-scroll">
            <table class="table-list">
                <thead><tr><th>Position</th><th>Section</th><th>Content source</th><th>Visibility</th><th>Manage</th></tr></thead>
                <tbody>
                    @forelse($homepageSections as $section)
                        <tr>
                            <td>{{ $section->sort_order }}</td>
                            <td><strong>{{ $section->title }}</strong><small class="table-subtitle">{{ $section->key }}</small></td>
                            <td>{{ data_get($section->config, 'count', 5) }} latest stories</td>
                            <td><span class="status-badge {{ $section->is_active ? 'status-active' : 'status-inactive' }}">{{ $section->is_active ? 'Visible' : 'Hidden' }}</span></td>
                            <td class="row-actions">
                                <details class="row-edit"><summary>Edit</summary>
                                    <form method="POST" action="{{ route('admin.homepage.update', $section) }}" class="form-grid row-edit-form">
                                        @csrf @method('PUT')
                                        <label>Title <input name="title" value="{{ $section->title }}" required></label>
                                        <label>Story count <input type="number" name="count" value="{{ data_get($section->config, 'count', 5) }}" min="1" max="50" required></label>
                                        <label>Position <input type="number" name="sort_order" value="{{ $section->sort_order }}" min="0" required></label>
                                        <label class="check-list"><input type="checkbox" name="is_active" value="1" @checked($section->is_active)> Visible</label>
                                        <button class="button button-primary" type="submit">Save section</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.homepage.delete', $section) }}" onsubmit="return confirm('Remove this homepage section?')">@csrf @method('DELETE')<button class="link-button" type="submit">Remove</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state"><strong>No configured sections</strong><p>Add a section to begin arranging homepage content.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
