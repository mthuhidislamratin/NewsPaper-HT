@extends('admin.layout')

@section('title', 'Articles')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Editorial desk</p>
            <h1>Articles</h1>
            <p class="page-description">Create, review, schedule, and manage newsroom coverage.</p>
        </div>
        <a class="button button-primary" href="#new-article">Write an article</a>
    </div>

    <details class="panel article-create" id="new-article" @if($errors->any()) open @endif>
        <summary><span>Create article</span><span class="muted">Start a draft or send a story to the publication queue</span></summary>
        <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data" class="form-grid form-grid-wide">
            @csrf
            <label>Headline <input type="text" name="title" value="{{ old('title') }}" maxlength="255" required>@error('title')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Custom slug <input type="text" name="slug" value="{{ old('slug') }}" placeholder="generated-from-headline"></label>
            <label>Category
                <select name="category_id">
                    <option value="">Unassigned</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<small class="field-error">{{ $message }}</small>@enderror
            </label>
            <label>Tags
                <select name="tags[]" multiple size="4" aria-describedby="tag-help">
                    @foreach(\App\Models\Tag::query()->where('is_active', true)->orderBy('name')->get() as $tag)
                        <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tags', [])))>{{ $tag->name }}</option>
                    @endforeach
                </select>
                <small id="tag-help" class="muted">Use Ctrl or Command to select multiple tags.</small>
            </label>
            <label class="form-span-2">Excerpt <textarea name="excerpt" rows="3" maxlength="1000">{{ old('excerpt') }}</textarea></label>
            <label class="form-span-2">Article body <textarea name="content" rows="12" required>{{ old('content') }}</textarea>@error('content')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>SEO title <input type="text" name="seo_title" maxlength="255" value="{{ old('seo_title') }}"></label>
            <label>Meta description <textarea name="meta_description" rows="3" maxlength="500">{{ old('meta_description') }}</textarea></label>
            <label>Editorial status
                <select name="status" id="create-article-status">
                    @foreach(['draft' => 'Draft', 'review' => 'In review', 'fact_check' => 'Fact check', 'approved' => 'Approved', 'scheduled' => 'Scheduled', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', 'draft') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Schedule for <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}">@error('scheduled_at')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Upload featured image <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif">@error('featured_image')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Or reuse library image
                <select name="media_id"><option value="">Choose existing image</option>@foreach($mediaLibrary as $mediaItem)<option value="{{ $mediaItem->id }}" @selected(old('media_id') == $mediaItem->id)>{{ $mediaItem->name }}</option>@endforeach</select>
            </label>
            <div class="check-list form-span-2">
                <label><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))> Lead story</label>
                <label><input type="checkbox" name="is_breaking" value="1" @checked(old('is_breaking'))> Breaking news</label>
                <label><input type="checkbox" name="is_trending" value="1" @checked(old('is_trending'))> Trending</label>
            </div>
            <div class="form-span-2"><button class="button button-primary" type="submit">Save article</button></div>
        </form>
    </details>

    <form method="GET" action="{{ route('admin.articles') }}" class="filter-bar panel">
        <label class="filter-search">Search <input type="search" name="q" value="{{ request('q') }}" placeholder="Headline"></label>
        <label>Status
            <select name="status">
                <option value="">All statuses</option>
                @foreach(['draft', 'review', 'fact_check', 'approved', 'scheduled', 'published', 'archived'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                @endforeach
                <option value="trashed" @selected(request('status') === 'trashed')>Trash</option>
            </select>
        </label>
        <label>Category
            <select name="category_id"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select>
        </label>
        <label>Author
            <select name="author_id"><option value="">All authors</option>@foreach($authors as $author)<option value="{{ $author->id }}" @selected(request('author_id') == $author->id)>{{ $author->name }}</option>@endforeach</select>
        </label>
        <button class="button button-secondary" type="submit">Apply</button>
        <a class="button button-ghost" href="{{ route('admin.articles') }}">Clear</a>
    </form>

    <form method="POST" action="{{ route('admin.articles.bulk') }}" class="panel table-panel">
        @csrf
        <div class="table-toolbar">
            <div><h2>All articles</h2><span class="muted">{{ $articles->total() }} results</span></div>
            <div class="bulk-actions">
                <label class="sr-only" for="bulk-action">Bulk action</label>
                <select id="bulk-action" name="action" required>
                    <option value="">Bulk action</option>
                    @if(request('status') === 'trashed')
                        <option value="restore">Restore</option>
                    @else
                        <option value="publish">Publish</option>
                        <option value="unpublish">Move to draft</option>
                        <option value="archive">Archive</option>
                        <option value="delete">Move to trash</option>
                    @endif
                </select>
                <button class="button button-secondary" type="submit" onclick="return confirm('Apply this action to the selected articles?')">Apply</button>
            </div>
        </div>
        <div class="table-scroll">
            <table class="table-list">
                <thead><tr><th><span class="sr-only">Select</span></th><th>Article</th><th>Status</th><th>Category</th><th>Author</th><th>Views</th><th>Published</th><th>Updated</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td><input type="checkbox" name="article_ids[]" value="{{ $article->id }}" aria-label="Select {{ $article->title }}"></td>
                            <td><strong>{{ $article->title }}</strong><small class="table-subtitle">/{{ $article->slug }}</small></td>
                            <td><span class="status-badge status-{{ $article->trashed() ? 'trashed' : $article->status }}">{{ $article->trashed() ? 'In trash' : str($article->status)->replace('_', ' ')->title() }}</span></td>
                            <td>{{ $article->category?->name ?? 'Unassigned' }}</td>
                            <td>{{ $article->author?->name ?? 'Staff' }}</td>
                            <td>{{ number_format($article->views) }}</td>
                            <td>{{ $article->published_at?->format('M j, Y') ?? '—' }}</td>
                            <td>{{ $article->updated_at->format('M j, Y') }}</td>
                            <td class="row-actions">
                                @if($article->trashed())
                                    <button class="link-button" type="submit" formaction="{{ route('admin.articles.restore', $article->id) }}" formmethod="POST">Restore</button>
                                @else
                                    <a href="{{ route('admin.articles.edit', $article) }}">Edit</a>
                                    <a href="{{ route('article.show', $article) }}" target="_blank" rel="noopener">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9"><div class="empty-state"><strong>No articles found</strong><p>Change the filters or write your first story.</p><a href="#new-article">Create an article</a></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $articles->links() }}</div>
    </form>
@endsection
