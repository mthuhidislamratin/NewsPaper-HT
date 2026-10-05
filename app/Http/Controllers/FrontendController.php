<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Article;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function index(): View
    {
        $sections = HomepageSection::query()->where('is_active', true)->orderBy('sort_order')->get()->keyBy('key');
        $limitFor = fn (string $key, int $fallback): int => (int) data_get($sections->get($key)?->config, 'count', $fallback);
        $breaking = Article::visible()->where('is_breaking', true)->latest('published_at')->limit($limitFor('breaking_news', 5))->get();
        $featured = Article::visible()->where('is_featured', true)->with(['category', 'author'])->latest('published_at')->limit($limitFor('featured_stories', 4))->get();
        $latest = Article::visible()->with(['category', 'author'])->latest('published_at')->limit($limitFor('latest_news', 8))->get();
        if ($featured->isEmpty()) {
            $featured = $latest->take(4);
        }
        $categories = Category::query()
            ->where('is_active', true)
            ->with(['articles' => fn ($query) => $query->visible()->latest('published_at')->limit(3)])
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
        $trending = Article::visible()->where('is_trending', true)->latest('published_at')->limit($limitFor('trending', 5))->get();
        $mostRead = Article::visible()->with('category')->orderByDesc('views')->limit($limitFor('most_read', 5))->get();
        $sidebarAd = Advertisement::query()->active()->where('placement', 'sidebar')->first();
        $topAd = Advertisement::query()->active()->where('placement', 'header')->first();

        return view('frontend.home', compact('breaking', 'featured', 'latest', 'categories', 'trending', 'mostRead', 'sidebarAd', 'topAd', 'sections'));
    }

    public function article(Article $article): View
    {
        abort_unless($article->status === 'published' && $article->is_published, 404);

        $article->increment('views');
        $related = Article::visible()->where('id', '!=', $article->id)->where('category_id', $article->category_id)->limit(4)->get();
        $ad = Advertisement::query()->active()->where('placement', 'content_top')->first();

        return view('frontend.article', compact('article', 'related', 'ad'));
    }

    public function category(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $articles = Article::visible()->where('category_id', $category->id)->latest('published_at')->paginate(10);

        return view('frontend.category', compact('category', 'articles'));
    }

    public function tag(string $slug): View|\Illuminate\Http\RedirectResponse
    {
        $tag = Tag::query()->where('slug', $slug)->where('is_active', true)->first();

        if (! $tag) {
            $redirect = \Illuminate\Support\Facades\DB::table('tag_redirects')
                ->join('tags', 'tag_redirects.target_tag_id', '=', 'tags.id')
                ->where('tag_redirects.source_slug', $slug)
                ->first(['tags.slug as target_slug']);
            abort_unless($redirect, 404);

            return redirect()->route('tag.show', $redirect->target_slug, 301);
        }

        $articles = $tag->articles()->visible()->latest('published_at')->paginate(10);

        return view('frontend.tag', compact('tag', 'articles'));
    }

    public function search(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sort' => ['nullable', Rule::in(['recent', 'popular'])],
        ]);
        $query = trim((string) ($filters['q'] ?? ''));
        $search = Article::query()->visible()->with(['category', 'author']);
        if ($query !== '') {
            $search->search($query);
        } else {
            $search->whereRaw('1 = 0');
        }
        $search->when(isset($filters['category_id']), fn ($builder) => $builder->where('category_id', $filters['category_id']));
        $search->when(isset($filters['author_id']), fn ($builder) => $builder->where('author_id', $filters['author_id']));
        $search->when(isset($filters['from']), fn ($builder) => $builder->whereDate('published_at', '>=', $filters['from']));
        $search->when(isset($filters['to']), fn ($builder) => $builder->whereDate('published_at', '<=', $filters['to']));

        if (($filters['sort'] ?? 'recent') === 'popular') {
            $search->orderByDesc('views');
        } else {
            $search->latest('published_at');
        }

        $articles = $search->paginate(10)->withQueryString();
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $authors = User::query()->whereHas('articles', fn ($builder) => $builder->visible())->orderBy('name')->get();

        return view('frontend.search', compact('query', 'articles', 'categories', 'authors'));
    }

    public function author(string $slug): View
    {
        $user = \App\Models\User::where('username', $slug)->orWhere('name', $slug)->firstOrFail();
        $articles = Article::visible()->where('author_id', $user->id)->latest('published_at')->paginate(10);

        return view('frontend.author', compact('user', 'articles'));
    }
}
