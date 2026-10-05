<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Article;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'articles' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'drafts' => Article::where('status', 'draft')->count(),
            'scheduled' => Article::where('status', 'scheduled')->count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'media' => Media::count(),
            'ads' => Advertisement::count(),
        ];

        $recentArticles = Article::with('category')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentArticles'));
    }

    public function audit(Request $request): View
    {
        $query = DB::table('newsroom_audit_events')
            ->leftJoin('users', 'newsroom_audit_events.actor_id', '=', 'users.id')
            ->select('newsroom_audit_events.*', 'users.name as actor_name')
            ->orderByDesc('newsroom_audit_events.created_at');
        $query->when($request->filled('action'), fn ($builder) => $builder->where('action', $request->string('action')));
        $query->when($request->filled('q'), function ($builder) use ($request) {
            $term = '%'.$request->string('q')->trim().'%';
            $builder->where(function ($search) use ($term) {
                $search->where('users.name', 'ilike', $term)
                    ->orWhere('newsroom_audit_events.subject_type', 'ilike', $term)
                    ->orWhere('newsroom_audit_events.action', 'ilike', $term);
            });
        });

        $events = $query->paginate(30)->withQueryString();
        $actions = DB::table('newsroom_audit_events')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit', compact('events', 'actions'));
    }

    public function articles(Request $request): View
    {
        $query = $request->input('status') === 'trashed'
            ? Article::onlyTrashed()->with('category', 'author', 'tags')
            : Article::with('category', 'author', 'tags');
        $query->when($request->filled('q'), fn ($builder) => $builder->where('title', 'ilike', '%'.$request->string('q')->trim().'%'));
        $query->when($request->filled('status') && $request->input('status') !== 'trashed', fn ($builder) => $builder->where('status', $request->string('status')));
        $query->when($request->filled('category_id'), fn ($builder) => $builder->where('category_id', $request->integer('category_id')));
        $query->when($request->filled('author_id'), fn ($builder) => $builder->where('author_id', $request->integer('author_id')));
        if (auth()->user()->hasAnyRole(['Journalist', 'Author'])) {
            $query->where('author_id', auth()->id());
        }

        $articles = $query->latest('updated_at')->paginate(15)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();
        $authors = User::query()->whereHas('articles')->orderBy('name')->get();
        $mediaLibrary = Media::query()->where('mime_type', 'like', 'image/%')->latest()->limit(100)->get();

        return view('admin.articles', compact('articles', 'categories', 'authors', 'mediaLibrary'));
    }

    public function editArticle(Article $article): View
    {
        $this->authorizeArticleAccess($article);

        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $tags = Tag::query()->where('is_active', true)->orderBy('name')->get();
        $mediaLibrary = Media::query()->where('mime_type', 'like', 'image/%')->latest()->limit(100)->get();
        $revisions = DB::table('article_revisions')
            ->leftJoin('users', 'article_revisions.user_id', '=', 'users.id')
            ->where('article_revisions.article_id', $article->id)
            ->latest('article_revisions.created_at')
            ->limit(20)
            ->get(['article_revisions.id', 'article_revisions.snapshot', 'article_revisions.created_at', 'users.name as editor_name']);

        return view('admin.articles-edit', compact('article', 'categories', 'tags', 'revisions', 'mediaLibrary'));
    }

    public function categories(): View
    {
        $categories = Category::query()->with('parent')->withCount('articles')->orderBy('sort_order')->get();
        $parentCategories = Category::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.categories', compact('categories', 'parentCategories'));
    }

    public function tags(): View
    {
        $tags = Tag::query()->withCount('articles')->orderBy('name')->get();

        return view('admin.tags', compact('tags'));
    }

    public function media(Request $request): View
    {
        $query = Media::query()->latest();
        $query->when($request->filled('q'), function ($builder) use ($request) {
            $term = '%'.$request->string('q')->trim().'%';
            $builder->where(function ($search) use ($term) {
                $search->where('name', 'ilike', $term)
                    ->orWhereRaw('custom_properties::text ILIKE ?', [$term]);
            });
        });
        $media = $query->paginate(24)->withQueryString();

        return view('admin.media', compact('media'));
    }

    public function advertisements(): View
    {
        $advertisements = Advertisement::query()->orderBy('sort_order')->get();

        return view('admin.advertisements', compact('advertisements'));
    }

    public function homepage(): View
    {
        $sections = HomepageSection::query()->orderBy('sort_order')->get();
        $articles = Article::query()->where('status', 'published')->orderBy('published_at', 'desc')->get();

        $homepageSections = $sections;

        return view('admin.homepage', compact('homepageSections', 'articles'));
    }

    public function settings(): View
    {
        $settings = SiteSetting::query()->get()->pluck('value', 'key')->all();

        return view('admin.settings', compact('settings'));
    }

    public function seo(): View
    {
        $articles = Article::query()->latest()->get();

        return view('admin.seo', compact('articles'));
    }

    public function users(): View
    {
        $users = User::query()->with('roles')->paginate(20);
        $roles = Role::all();

        return view('admin.users', compact('users', 'roles'));
    }

    public function roles(): View
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.roles', compact('roles', 'permissions'));
    }

    public function permissions(): View
    {
        $permissions = Permission::all();
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('admin.permissions', compact('permissions', 'roles'));
    }

    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        Category::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: $this->uniqueSlug('categories', $validated['name']),
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $this->recordAudit('category.created', Category::query()->latest('id')->firstOrFail(), ['name' => $validated['name']]);

        return redirect()->route('admin.categories')->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category->id])],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $parent = isset($validated['parent_id']) ? Category::query()->find($validated['parent_id']) : null;
        while ($parent) {
            if ($parent->id === $category->id) {
                return back()->withErrors(['parent_id' => 'A category cannot be nested beneath itself or one of its descendants.'])->withInput();
            }
            $parent = $parent->parent;
        }

        $category->update([
            ...$validated,
            'slug' => ($validated['slug'] ?? null) ?: $this->uniqueSlug('categories', $validated['name'], $category->id),
            'is_active' => $request->boolean('is_active'),
        ]);
        $this->recordAudit('category.updated', $category, ['name' => $category->name, 'is_active' => $category->is_active]);

        return redirect()->route('admin.categories')->with('success', 'Category updated.');
    }

    public function deleteCategory(Category $category)
    {
        if ($category->articles()->exists() || $category->children()->exists()) {
            return redirect()->route('admin.categories')->withErrors([
                'category' => 'Reassign its articles and child categories before deleting this category.',
            ]);
        }

        $category->delete();
        $this->recordAudit('category.archived', $category);

        return redirect()->route('admin.categories')->with('success', 'Category archived.');
    }

    public function createTag(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:tags,slug'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
        ]);

        Tag::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: $this->uniqueSlug('tags', $validated['name']),
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'is_active' => true,
            'sort_order' => 0,
        ]);
        $this->recordAudit('tag.created', Tag::query()->latest('id')->firstOrFail(), ['name' => $validated['name']]);

        return redirect()->route('admin.tags')->with('success', 'Tag created.');
    }

    public function updateTag(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('tags', 'slug')->ignore($tag->id)],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $tag->update([
            ...$validated,
            'slug' => ($validated['slug'] ?? null) ?: $this->uniqueSlug('tags', $validated['name'], $tag->id),
            'is_active' => $request->boolean('is_active'),
        ]);
        $this->recordAudit('tag.updated', $tag, ['name' => $tag->name, 'is_active' => $tag->is_active]);

        return redirect()->route('admin.tags')->with('success', 'Tag updated.');
    }

    public function deleteTag(Tag $tag)
    {
        $tag->articles()->detach();
        $tag->delete();
        $this->recordAudit('tag.archived', $tag);

        return redirect()->route('admin.tags')->with('success', 'Tag archived and removed from articles.');
    }

    public function mergeTag(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'target_tag_id' => ['required', 'integer', 'exists:tags,id', Rule::notIn([$tag->id])],
        ]);

        $target = Tag::query()->findOrFail($validated['target_tag_id']);

        DB::transaction(function () use ($tag, $target) {
            $target->articles()->syncWithoutDetaching($tag->articles()->pluck('articles.id')->all());
            $tag->articles()->detach();
            DB::table('tag_redirects')->updateOrInsert(
                ['source_slug' => $tag->slug],
                ['target_tag_id' => $target->id, 'created_at' => now(), 'updated_at' => now()]
            );
            $tag->delete();
        });
        $this->recordAudit('tag.merged', $target, ['source_slug' => $tag->slug]);

        return redirect()->route('admin.tags')->with('success', 'Tag merged into '.$target->name.'.');
    }

    public function createArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:articles,slug'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'distinct', 'exists:tags,id'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'review', 'fact_check', 'approved', 'scheduled', 'published', 'archived'])],
            'scheduled_at' => ['nullable', 'date', Rule::requiredIf($request->input('status') === 'scheduled')],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'media_id' => ['nullable', 'integer', 'exists:media,id'],
        ]);

        abort_unless(auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Journalist', 'Author']), 403);
        abort_if(
            auth()->user()->hasAnyRole(['Journalist', 'Author']) && ! in_array($validated['status'], ['draft', 'review'], true),
            403,
            'Authors may save a draft or submit it for review; an editor publishes approved work.'
        );

        if ($validated['status'] === 'scheduled' && now()->greaterThanOrEqualTo($validated['scheduled_at'])) {
            return back()->withErrors(['scheduled_at' => 'Choose a publication time in the future.'])->withInput();
        }

        $article = Article::create([
            'author_id' => auth()->id(),
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'slug' => ($validated['slug'] ?? null) ?: $this->uniqueArticleSlug($validated['title']),
            'status' => $validated['status'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'seo_title' => $validated['seo_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_breaking' => $request->boolean('is_breaking'),
            'is_trending' => $request->boolean('is_trending'),
            'is_published' => $validated['status'] === 'published',
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'scheduled_at' => $validated['status'] === 'scheduled' ? $validated['scheduled_at'] : null,
        ]);

        $article->tags()->sync($validated['tags'] ?? []);

        if ($request->hasFile('featured_image')) {
            $article->addMedia($request->file('featured_image'))->toMediaCollection('featured');
            $article->featured_media_id = $article->getFirstMedia('featured')?->id;
            $article->save();
        } elseif (! empty($validated['media_id'])) {
            $this->attachLibraryMedia($article, (int) $validated['media_id']);
        }
        $this->recordAudit('article.created', $article, ['status' => $article->status, 'title' => $article->title]);

        return redirect()->route('admin.articles')->with('success', 'Article created.');
    }

    public function updateArticle(Request $request, Article $article)
    {
        $this->authorizeArticleAccess($article);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('articles', 'slug')->ignore($article->id)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'distinct', 'exists:tags,id'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'scheduled_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'media_id' => ['nullable', 'integer', 'exists:media,id'],
        ]);

        $snapshot = $article->only([
            'title', 'slug', 'category_id', 'status', 'excerpt', 'content', 'seo_title',
            'meta_description', 'is_featured', 'is_breaking', 'is_trending',
        ]);
        $snapshot['tags'] = $article->tags()->pluck('tags.id')->all();

        DB::table('article_revisions')->insert([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);

        $article->update([
            ...collect($validated)->except(['tags', 'featured_image'])->all(),
            'is_featured' => $request->boolean('is_featured'),
            'is_breaking' => $request->boolean('is_breaking'),
            'is_trending' => $request->boolean('is_trending'),
        ]);
        $article->tags()->sync($validated['tags'] ?? []);

        if ($request->hasFile('featured_image')) {
            $article->addMedia($request->file('featured_image'))->toMediaCollection('featured');
            $article->featured_media_id = $article->getFirstMedia('featured')?->id;
            $article->save();
        } elseif (! empty($validated['media_id'])) {
            $this->attachLibraryMedia($article, (int) $validated['media_id']);
        }
        $this->recordAudit('article.updated', $article, ['title' => $article->title]);

        return redirect()->route('admin.articles.edit', $article)->with('success', 'Article changes saved.');
    }

    public function deleteArticle(Article $article)
    {
        abort_unless(auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor']), 403);
        $article->delete();
        $this->recordAudit('article.trashed', $article);

        return redirect()->route('admin.articles')->with('success', 'Article archived in trash.');
    }

    public function restoreArticle(int $articleId)
    {
        abort_unless(auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor']), 403);
        $article = Article::onlyTrashed()->findOrFail($articleId);
        $article->restore();
        $this->recordAudit('article.restored', $article);

        return redirect()->route('admin.articles')->with('success', 'Article restored.');
    }

    public function bulkArticles(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor']), 403);

        $validated = $request->validate([
            'article_ids' => ['required', 'array', 'min:1'],
            'article_ids.*' => ['integer', 'distinct'],
            'action' => ['required', Rule::in(['publish', 'unpublish', 'archive', 'delete', 'restore'])],
        ]);

        $articles = ($validated['action'] === 'restore' ? Article::onlyTrashed() : Article::query())
            ->whereIn('id', $validated['article_ids'])
            ->get();
        abort_if($articles->count() !== count($validated['article_ids']), 404);

        foreach ($articles as $article) {
            if ($validated['action'] === 'delete') {
                $article->delete();
                $this->recordAudit('article.trashed', $article, ['bulk' => true]);
                continue;
            }
            if ($validated['action'] === 'restore') {
                $article->restore();
                $this->recordAudit('article.restored', $article, ['bulk' => true]);
                continue;
            }

            $article->status = match ($validated['action']) {
                'publish' => 'published',
                'unpublish' => 'draft',
                'archive' => 'archived',
            };
            $article->is_published = $validated['action'] === 'publish';

            if ($validated['action'] === 'publish' && ! $article->published_at) {
                $article->published_at = now();
            }

            $article->save();
            $this->recordAudit('article.'.$validated['action'].'d', $article, ['bulk' => true]);
        }

        return redirect()->route('admin.articles')->with('success', count($articles).' articles updated.');
    }

    public function transitionArticle(Request $request, Article $article)
    {
        $user = auth()->user();
        $this->authorizeArticleAccess($article, allowReviewer: true);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'review', 'fact_check', 'approved', 'scheduled', 'published', 'archived'])],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        if ($user->hasAnyRole(['Journalist', 'Author']) && ! in_array($validated['status'], ['draft', 'review'], true)) {
            abort(403, 'Only an editor can approve or publish this article.');
        }

        if ($user->hasRole('Reviewer') && ! in_array($validated['status'], ['review', 'fact_check', 'approved'], true)) {
            abort(403, 'Reviewers can move work through review, fact check, and approval only.');
        }

        if ($validated['status'] === 'scheduled' && (! isset($validated['scheduled_at']) || now()->greaterThanOrEqualTo($validated['scheduled_at']))) {
            return back()->withErrors(['scheduled_at' => 'Choose a publication time in the future.']);
        }

        $snapshot = $article->only(['title', 'status', 'published_at', 'scheduled_at']);
        $snapshot['tags'] = $article->tags()->pluck('tags.id')->all();
        DB::table('article_revisions')->insert([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'snapshot' => json_encode($snapshot, JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);

        $wasPublished = $article->status === 'published';
        $article->status = $validated['status'];
        $article->is_published = $validated['status'] === 'published';
        $article->scheduled_at = $validated['status'] === 'scheduled' ? $validated['scheduled_at'] : null;

        if ($validated['status'] === 'published' && ! $wasPublished) {
            $article->published_at = now();
        }

        $article->save();
        $this->recordAudit('article.status_changed', $article, ['status' => $article->status]);

        return back()->with('success', 'Article status updated to '.str_replace('_', ' ', $article->status).'.');
    }

    public function restoreArticleRevision(Request $request, int $revision)
    {
        $record = DB::table('article_revisions')->where('id', $revision)->first();
        abort_unless($record, 404);
        $article = Article::query()->findOrFail($record->article_id);
        $this->authorizeArticleAccess($article);
        $snapshot = json_decode($record->snapshot, true, flags: JSON_THROW_ON_ERROR);
        $fields = collect($snapshot)->only([
            'title', 'slug', 'category_id', 'status', 'excerpt', 'content', 'seo_title',
            'meta_description', 'is_featured', 'is_breaking', 'is_trending',
        ])->all();

        DB::transaction(function () use ($article, $snapshot, $fields) {
            DB::table('article_revisions')->insert([
                'article_id' => $article->id,
                'user_id' => auth()->id(),
                'snapshot' => json_encode($article->only([
                    'title', 'slug', 'category_id', 'status', 'excerpt', 'content', 'seo_title',
                    'meta_description', 'is_featured', 'is_breaking', 'is_trending',
                ]), JSON_THROW_ON_ERROR),
                'created_at' => now(),
            ]);
            $article->update([
                ...$fields,
                'is_published' => ($fields['status'] ?? 'draft') === 'published',
            ]);
            $article->tags()->sync($snapshot['tags'] ?? []);
        });

        $this->recordAudit('article.revision_restored', $article, ['revision_id' => $revision]);

        return redirect()->route('admin.articles.edit', $article)->with('success', 'Article revision restored.');
    }

    public function updateMedia(Request $request, Media $media)
    {
        $validated = $request->validate([
            'alt_text' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $media->setCustomProperty('alt_text', $validated['alt_text']);
        $media->setCustomProperty('caption', $validated['caption'] ?? null);
        $media->save();
        $this->recordAudit('media.metadata_updated', $media);

        return redirect()->route('admin.media')->with('success', 'Media metadata updated.');
    }

    private function uniqueArticleSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $suffix = 2;

        while (Article::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function uniqueSlug(string $table, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'section';
        $slug = $base;
        $suffix = 2;

        while (DB::table($table)->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function authorizeArticleAccess(Article $article, bool $allowReviewer = false): void
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Editor'])) {
            return;
        }

        if ($allowReviewer && $user->hasRole('Reviewer')) {
            return;
        }

        abort_unless(
            $user->hasAnyRole(['Journalist', 'Author']) && $article->author_id === $user->id,
            403
        );
    }

    public function createAdvertisement(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'placement' => ['required', Rule::in(['header', 'sidebar', 'content_top', 'in_article', 'footer', 'mobile_sticky'])],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'target_url' => ['nullable', 'url', 'max:2048'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Advertisement::create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);
        $this->recordAudit('advertisement.created', Advertisement::query()->latest('id')->firstOrFail(), ['placement' => $validated['placement']]);

        return redirect()->route('admin.advertisements')->with('success', 'Advertisement created.');
    }

    public function updateAdvertisement(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'placement' => ['required', Rule::in(['header', 'sidebar', 'content_top', 'in_article', 'footer', 'mobile_sticky'])],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'target_url' => ['nullable', 'url', 'max:2048'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $advertisement->update([...$validated, 'is_active' => $request->boolean('is_active')]);
        $this->recordAudit('advertisement.updated', $advertisement, ['placement' => $advertisement->placement, 'is_active' => $advertisement->is_active]);

        return redirect()->route('admin.advertisements')->with('success', 'Advertisement updated.');
    }

    public function deleteAdvertisement(Advertisement $advertisement)
    {
        $advertisement->delete();
        $this->recordAudit('advertisement.archived', $advertisement);

        return redirect()->route('admin.advertisements')->with('success', 'Advertisement archived.');
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'site_email' => ['nullable', 'email', 'max:255'],
            'default_timezone' => ['required', 'timezone'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'string']);
        }

        return redirect()->route('admin.settings')->with('success', 'Settings saved.');
    }

    public function saveHomepage(Request $request)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:homepage_sections,key'],
            'title' => ['required', 'string', 'max:255'],
            'count' => ['nullable', 'integer', 'min:1', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        HomepageSection::create([
            'key' => $validated['key'],
            'title' => $validated['title'],
            'config' => ['count' => $validated['count'] ?? 5],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);
        $this->recordAudit('homepage_section.created', HomepageSection::query()->latest('id')->firstOrFail(), ['key' => $validated['key']]);

        return redirect()->route('admin.homepage')->with('success', 'Homepage section added.');
    }

    public function updateHomepageSection(Request $request, HomepageSection $section)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:1', 'max:50'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $section->update([
            'title' => $validated['title'],
            'config' => ['count' => $validated['count']],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);
        $this->recordAudit('homepage_section.updated', $section);

        return redirect()->route('admin.homepage')->with('success', 'Homepage section updated.');
    }

    public function deleteHomepageSection(HomepageSection $section)
    {
        $section->delete();
        $this->recordAudit('homepage_section.deleted', $section);

        return redirect()->route('admin.homepage')->with('success', 'Homepage section removed.');
    }

    public function storeMedia(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:2048', 'mimetypes:image/jpeg,image/png,image/gif,image/webp'],
            'alt_text' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $media = auth()->user()->addMedia($request->file('file'))
            ->withCustomProperties([
                'alt_text' => $validated['alt_text'],
                'caption' => $validated['caption'] ?? null,
            ])
            ->toMediaCollection('uploads');
        $this->recordAudit('media.uploaded', $media, ['mime_type' => $media->mime_type]);

        return redirect()->route('admin.media')->with('success', 'Media uploaded.');
    }

    public function deleteMedia(Media $media)
    {
        if ($media->model_type === Article::class && Article::query()->where('featured_media_id', $media->id)->where('status', 'published')->exists()) {
            return redirect()->route('admin.media')->withErrors([
                'media' => 'This image is attached to a published article. Replace its image before deleting this asset.',
            ]);
        }

        $media->delete();
        $this->recordAudit('media.deleted', $media, ['file_name' => $media->file_name]);

        return redirect()->route('admin.media')->with('success', 'Media deleted.');
    }

    public function saveSeo(Request $request)
    {
        $validated = $request->validate([
            'page' => ['required', Rule::in(['home', 'site'])],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url'],
        ]);

        foreach (['meta_title', 'meta_description', 'canonical_url'] as $field) {
            SiteSetting::updateOrCreate(
                ['key' => 'seo_'.$validated['page'].'_'.$field],
                ['value' => $validated[$field] ?? null, 'type' => 'string']
            );
        }

        return redirect()->route('admin.seo')->with('success', 'SEO settings saved.');
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:191', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
            'role' => ['nullable', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ]);

        abort_if(($validated['role'] ?? null) === 'Super Admin' && ! auth()->user()->can('manage_roles'), 403);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? $this->uniqueUsername($validated['name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? null,
            'is_active' => true,
        ]);

        if (! empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }
        $this->recordAudit('user.created', $user, ['role' => $validated['role'] ?? null]);

        return redirect()->route('admin.users')->with('success', 'User created.');
    }

    public function updateUser(Request $request, User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Use profile settings to change your own account.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:191', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        abort_if($validated['role'] === 'Super Admin' && ! auth()->user()->can('manage_roles'), 403);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?: $this->uniqueUsername($validated['name'], $user),
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ]);
        $user->syncRoles([$validated['role']]);
        $this->recordAudit('user.updated', $user, ['role' => $validated['role'], 'is_active' => $user->is_active]);

        return redirect()->route('admin.users')->with('success', 'User profile and role updated.');
    }

    public function deactivateUser(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot deactivate your own account.');

        if ($user->hasRole('Super Admin') && User::query()->where('is_active', true)->whereKeyNot($user->id)->role('Super Admin')->doesntExist()) {
            return redirect()->route('admin.users')->withErrors(['user' => 'At least one active Super Admin must remain.']);
        }

        $user->update(['is_active' => false]);
        $this->recordAudit('user.deactivated', $user);

        return redirect()->route('admin.users')->with('success', 'User deactivated.');
    }

    public function createRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions(Permission::query()->whereIn('id', $validated['permissions'] ?? [])->get());
        $this->recordAudit('role.created', $role, ['permission_count' => $role->permissions()->count()]);

        return redirect()->route('admin.roles')->with('success', 'Role created.');
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        abort_if($role->name === 'Super Admin', 403, 'The Super Admin role is protected from permission changes.');

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ]);

        $role->syncPermissions(Permission::query()->whereIn('id', $validated['permissions'] ?? [])->get());
        $this->recordAudit('role.permissions_updated', $role, ['permission_count' => $role->permissions()->count()]);

        return redirect()->route('admin.roles')->with('success', 'Role permissions updated.');
    }

    private function uniqueUsername(string $name, ?User $except = null): string
    {
        $base = Str::slug($name) ?: 'user';
        $username = $base;
        $suffix = 2;

        while (User::query()->where('username', $username)->when($except, fn ($query) => $query->whereKeyNot($except->id))->exists()) {
            $username = $base.'-'.$suffix++;
        }

        return $username;
    }

    private function recordAudit(string $action, Model $subject, array $changes = []): void
    {
        DB::table('newsroom_audit_events')->insert([
            'actor_id' => auth()->id(),
            'action' => $action,
            'subject_type' => class_basename($subject),
            'subject_id' => $subject->getKey(),
            'ip_address' => request()->ip(),
            'changes' => json_encode($changes, JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);
    }

    private function attachLibraryMedia(Article $article, int $mediaId): void
    {
        $source = Media::query()->where('mime_type', 'like', 'image/%')->findOrFail($mediaId);
        $attached = $source->copy($article, 'featured');
        $article->featured_media_id = $attached->id;
        $article->save();
    }
}
