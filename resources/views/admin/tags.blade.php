@extends('admin.layout')

@section('title', 'Tags')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Content taxonomy</p><h1>Tags</h1><p class="page-description">Maintain topic labels and consolidate duplicates without breaking reader links.</p></div></div>
    <section class="panel">
        <h2>Create tag</h2>
        <form method="POST" action="{{ route('admin.tags.store') }}" class="form-grid form-grid-wide">
            @csrf
            <label>Name <input type="text" name="name" value="{{ old('name') }}" maxlength="255" required></label>
            <label>Slug <input type="text" name="slug" value="{{ old('slug') }}" placeholder="generated-from-name"></label>
            <label class="form-span-2">Description <textarea name="description" rows="2">{{ old('description') }}</textarea></label>
            <label>SEO title <input type="text" name="seo_title" value="{{ old('seo_title') }}" maxlength="255"></label>
            <label>Meta description <textarea name="meta_description" rows="2" maxlength="500">{{ old('meta_description') }}</textarea></label>
            <label class="form-span-2">Canonical URL <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" maxlength="2048"></label>
            <div class="form-span-2"><button class="button button-primary" type="submit">Add tag</button></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>All tags</h2><span class="muted">{{ $tags->count() }} topics</span></div></div>
        <div class="table-scroll">
            <table class="table-list">
                <thead><tr><th>Tag</th><th>Slug</th><th>Article usage</th><th>Manage</th></tr></thead>
                <tbody>
                    @forelse($tags as $tag)
                        <tr>
                            <td><strong>{{ $tag->name }}</strong></td><td>{{ $tag->slug }}</td><td>{{ $tag->articles_count }}</td>
                            <td class="row-actions">
                                <details class="row-edit"><summary>Edit</summary>
                                    <form method="POST" action="{{ route('admin.tags.update', $tag) }}" class="form-grid row-edit-form">
                                        @csrf @method('PUT')
                                        <label>Name <input name="name" value="{{ $tag->name }}" required></label>
                                        <label>Slug <input name="slug" value="{{ $tag->slug }}"></label>
                                        <label>Description <textarea name="description" rows="2">{{ $tag->description }}</textarea></label>
                                        <label>SEO title <input name="seo_title" value="{{ $tag->seo_title }}" maxlength="255"></label>
                                        <label>Meta description <textarea name="meta_description" rows="2" maxlength="500">{{ $tag->meta_description }}</textarea></label>
                                        <label>Canonical URL <input type="url" name="canonical_url" value="{{ $tag->canonical_url }}" maxlength="2048"></label>
                                        <label class="check-list"><input type="checkbox" name="is_active" value="1" @checked($tag->is_active)> Active</label>
                                        <button class="button button-primary" type="submit">Save tag</button>
                                    </form>
                                </details>
                                <details class="row-edit"><summary>Merge</summary>
                                    <form method="POST" action="{{ route('admin.tags.merge', $tag) }}" class="form-grid row-edit-form" onsubmit="return confirm('Move all articles to the selected tag and redirect this old tag URL?')">
                                        @csrf
                                        <label>Merge into
                                            <select name="target_tag_id" required>
                                                <option value="">Select destination</option>
                                                @foreach($tags->where('id', '!=', $tag->id)->where('is_active', true) as $target)<option value="{{ $target->id }}">{{ $target->name }}</option>@endforeach
                                            </select>
                                        </label>
                                        <button class="button button-secondary" type="submit">Merge tags</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.tags.delete', $tag) }}" onsubmit="return confirm('Remove this tag from all articles and archive it?')">@csrf @method('DELETE')<button class="link-button" type="submit">Archive</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state"><strong>No tags yet</strong><p>Create a topic label above.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
