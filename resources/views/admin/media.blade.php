@extends('admin.layout')

@section('title', 'Media library')

@section('content')
    <div class="page-heading"><div><p class="eyebrow">Digital asset management</p><h1>Media library</h1><p class="page-description">Upload, search, and reuse approved newsroom images.</p></div></div>
    <section class="panel">
        <h2>Upload an image</h2>
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="form-grid form-grid-wide">
            @csrf
            <label class="form-span-2">Image file <input type="file" name="file" accept="image/jpeg,image/png,image/gif,image/webp" required>@error('file')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Alternative text <input type="text" name="alt_text" maxlength="255" value="{{ old('alt_text') }}" required><small class="field-hint">Describe the relevant visual information for screen-reader users.</small></label>
            <label>Caption / credit <textarea name="caption" rows="2" maxlength="500">{{ old('caption') }}</textarea></label>
            <div class="form-span-2"><button class="button button-primary" type="submit">Upload to library</button></div>
        </form>
    </section>
    <form method="GET" action="{{ route('admin.media') }}" class="filter-bar panel">
        <label class="filter-search">Search assets <input type="search" name="q" value="{{ request('q') }}" placeholder="Filename, alt text, caption"></label>
        <button class="button button-secondary" type="submit">Search</button>
        <a class="button button-ghost" href="{{ route('admin.media') }}">Clear</a>
    </form>
    <section class="media-grid" aria-label="Media items">
        @forelse($media as $item)
            <article class="media-card panel">
                @if(str_starts_with($item->mime_type, 'image/'))
                    <img src="{{ $item->getUrl() }}" alt="{{ $item->getCustomProperty('alt_text') ?: $item->name }}">
                @endif
                <div class="media-card-body">
                    <strong>{{ $item->name }}</strong>
                    <span class="muted">{{ $item->mime_type }} · {{ number_format($item->size / 1024, 0) }} KB</span>
                    <p>{{ $item->getCustomProperty('alt_text') ?: 'Alt text missing' }}</p>
                    @if($item->getCustomProperty('caption'))<p class="muted">{{ $item->getCustomProperty('caption') }}</p>@endif
                    <details class="row-edit"><summary>Edit metadata</summary>
                        <form method="POST" action="{{ route('admin.media.update', $item) }}" class="form-grid row-edit-form">
                            @csrf @method('PATCH')
                            <label>Alternative text <input name="alt_text" value="{{ $item->getCustomProperty('alt_text') }}" required maxlength="255"></label>
                            <label>Caption / credit <textarea name="caption" maxlength="500">{{ $item->getCustomProperty('caption') }}</textarea></label>
                            <button class="button button-primary" type="submit">Save metadata</button>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('admin.media.delete', $item->id) }}" onsubmit="return confirm('Delete this asset? Files currently attached to published articles are protected.')">
                        @csrf @method('DELETE')
                        <button class="button button-danger" type="submit">Delete asset</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="panel empty-state"><strong>No media found</strong><p>Upload an image or change your search.</p></div>
        @endforelse
    </section>
    <div class="pagination-wrap">{{ $media->links() }}</div>
@endsection
