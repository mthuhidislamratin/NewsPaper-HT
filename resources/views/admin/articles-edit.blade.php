@extends('admin.layout')

@section('title', 'Edit article')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Editorial desk / {{ str($article->status)->replace('_', ' ')->title() }}</p><h1>Edit article</h1><p class="page-description">Last updated {{ $article->updated_at->diffForHumans() }}</p></div>
        <a class="button button-secondary" href="{{ route('article.show', $article) }}" target="_blank" rel="noopener">Preview public page</a>
    </div>

    <div class="editor-layout">
        <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" class="panel form-grid form-grid-wide">
            @csrf
            @method('PUT')
            <label class="form-span-2">Headline <input type="text" name="title" value="{{ old('title', $article->title) }}" maxlength="255" required>@error('title')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Slug <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" required>@error('slug')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Category
                <select name="category_id"><option value="">Unassigned</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $article->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select>
            </label>
            <label class="form-span-2">Tags
                <select name="tags[]" multiple size="5">
                    @foreach($tags as $tag)<option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tags', $article->tags->modelKeys())))>{{ $tag->name }}</option>@endforeach
                </select>
            </label>
            <label class="form-span-2">Excerpt <textarea name="excerpt" rows="3" maxlength="1000">{{ old('excerpt', $article->excerpt) }}</textarea></label>
            <label class="form-span-2">Article body <textarea name="content" rows="18" required>{{ old('content', $article->content) }}</textarea>@error('content')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>SEO title <input type="text" name="seo_title" maxlength="255" value="{{ old('seo_title', $article->seo_title) }}"></label>
            <label>Meta description <textarea name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $article->meta_description) }}</textarea></label>
            <label>Replace featured image <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif">@error('featured_image')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Or reuse library image
                <select name="media_id"><option value="">Keep current image</option>@foreach($mediaLibrary as $mediaItem)<option value="{{ $mediaItem->id }}">{{ $mediaItem->name }}</option>@endforeach</select>
            </label>
            @if($article->getFirstMediaUrl('featured'))
                <div class="image-preview"><img src="{{ $article->getFirstMediaUrl('featured') }}" alt="Current featured image for {{ $article->title }}"><span class="muted">Current featured image</span></div>
            @endif
            <div class="check-list form-span-2">
                <label><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured))> Lead story</label>
                <label><input type="checkbox" name="is_breaking" value="1" @checked(old('is_breaking', $article->is_breaking))> Breaking news</label>
                <label><input type="checkbox" name="is_trending" value="1" @checked(old('is_trending', $article->is_trending))> Trending</label>
            </div>
            <div class="form-span-2"><button type="submit" class="button button-primary">Save changes</button></div>
        </form>

        <aside class="editor-sidebar">
            <section class="panel">
                <h2>Publication workflow</h2>
                <p>Current status: <span class="status-badge status-{{ $article->status }}">{{ str($article->status)->replace('_', ' ')->title() }}</span></p>
                <form method="POST" action="{{ route('admin.articles.transition', $article) }}" class="form-grid">
                    @csrf
                    <label>Move to
                        <select name="status" required>
                            @foreach(['draft' => 'Draft', 'review' => 'In review', 'fact_check' => 'Fact check', 'approved' => 'Approved', 'scheduled' => 'Scheduled', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
                                <option value="{{ $value }}" @selected($article->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Schedule time <input type="datetime-local" name="scheduled_at" value="{{ $article->scheduled_at?->format('Y-m-d\TH:i') }}"></label>
                    <button type="submit" class="button button-secondary">Update status</button>
                </form>
                @error('scheduled_at')<small class="field-error">{{ $message }}</small>@enderror
            </section>
            <section class="panel">
                <h2>Revision history</h2>
                @forelse($revisions as $revision)
                    <div class="revision-item"><strong>{{ $revision->editor_name ?? 'Former user' }}</strong><span>{{ \Carbon\Carbon::parse($revision->created_at)->format('M j, Y g:i a') }}</span>
                        @php $revisionData = json_decode($revision->snapshot, true); @endphp
                        <span>{{ $revisionData['title'] ?? 'Previous version' }} · {{ str($revisionData['status'] ?? 'draft')->replace('_', ' ')->title() }}</span>
                        <form method="POST" action="{{ route('admin.articles.revisions.restore', $revision->id) }}" onsubmit="return confirm('Restore this version? The current version will be saved to history first.')">@csrf<button class="link-button" type="submit">Restore version</button></form>
                    </div>
                @empty
                    <p class="muted">No saved revisions yet. The previous version is recorded each time you save.</p>
                @endforelse
            </section>
            <form method="POST" action="{{ route('admin.articles.delete', $article) }}" onsubmit="return confirm('Move this article to trash?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="button button-danger button-full">Move to trash</button>
            </form>
        </aside>
    </div>
@endsection
