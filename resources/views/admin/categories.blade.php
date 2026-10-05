@extends('admin.layout')

@section('title', 'Categories')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Content taxonomy</p><h1>Categories</h1><p class="page-description">Organize reporting into clear sections and maintain a readable hierarchy.</p></div>
    </div>
    <section class="panel">
        <h2>Create category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="form-grid form-grid-wide">
            @csrf
            <label>Name <input type="text" name="name" value="{{ old('name') }}" maxlength="255" required></label>
            <label>Slug <input type="text" name="slug" value="{{ old('slug') }}" placeholder="generated-from-name"></label>
            <label>Parent section
                <select name="parent_id"><option value="">Top-level category</option>@foreach($parentCategories as $parent)<option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->name }}</option>@endforeach</select>
            </label>
            <label>Description <textarea name="description" rows="2">{{ old('description') }}</textarea></label>
            <label>SEO title <input type="text" name="seo_title" value="{{ old('seo_title') }}" maxlength="255"></label>
            <label>Meta description <textarea name="meta_description" rows="2" maxlength="500">{{ old('meta_description') }}</textarea></label>
            <label class="form-span-2">Canonical URL <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" maxlength="2048"></label>
            <div class="form-span-2"><button class="button button-primary" type="submit">Add category</button></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-toolbar"><div><h2>All categories</h2><span class="muted">{{ $categories->count() }} sections</span></div></div>
        <div class="table-scroll">
            <table class="table-list">
                <thead><tr><th>Category</th><th>Parent</th><th>Slug</th><th>Articles</th><th>Visibility</th><th>Manage</th></tr></thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ $category->parent?->name ?? 'Top level' }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->articles_count }}</td>
                            <td><span class="status-badge {{ $category->is_active ? 'status-active' : 'status-inactive' }}">{{ $category->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td class="row-actions">
                                <details class="row-edit"><summary>Edit</summary>
                                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="form-grid row-edit-form">
                                        @csrf @method('PUT')
                                        <label>Name <input name="name" value="{{ $category->name }}" maxlength="255" required></label>
                                        <label>Slug <input name="slug" value="{{ $category->slug }}"></label>
                                        <label>Parent <select name="parent_id"><option value="">Top level</option>@foreach($parentCategories as $parent)@unless($parent->id === $category->id)<option value="{{ $parent->id }}" @selected($category->parent_id === $parent->id)>{{ $parent->name }}</option>@endunless @endforeach</select></label>
                                        <label>Description <textarea name="description" rows="2">{{ $category->description }}</textarea></label>
                                        <label>SEO title <input name="seo_title" value="{{ $category->seo_title }}" maxlength="255"></label>
                                        <label>Meta description <textarea name="meta_description" rows="2" maxlength="500">{{ $category->meta_description }}</textarea></label>
                                        <label>Canonical URL <input type="url" name="canonical_url" value="{{ $category->canonical_url }}" maxlength="2048"></label>
                                        <label class="check-list"><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Active</label>
                                        <button class="button button-primary" type="submit">Save category</button>
                                    </form>
                                </details>
                                <form method="POST" action="{{ route('admin.categories.delete', $category) }}" onsubmit="return confirm('Archive this category? Categories containing stories or child sections cannot be archived.')">@csrf @method('DELETE')<button class="link-button" type="submit">Archive</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><strong>No categories yet</strong><p>Create the first section above.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
