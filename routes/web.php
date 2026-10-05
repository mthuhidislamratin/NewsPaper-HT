<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/article/{article:slug}', [FrontendController::class, 'article'])->name('article.show');
Route::get('/category/{category:slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/tag/{slug}', [FrontendController::class, 'tag'])->name('tag.show');
Route::get('/search', [FrontendController::class, 'search'])->name('search');
Route::get('/author/{slug}', [FrontendController::class, 'author'])->name('author.show');
Route::view('/about', 'frontend.about')->name('pages.about');
Route::view('/contact', 'frontend.contact')->name('pages.contact');
Route::view('/newsletter', 'frontend.newsletter')->name('pages.newsletter');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
});

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/articles', [AdminController::class, 'articles'])->name('articles');
    Route::post('/articles', [AdminController::class, 'createArticle'])->middleware('can:manage_articles')->name('articles.store');
    Route::post('/articles/bulk', [AdminController::class, 'bulkArticles'])->middleware('can:manage_articles')->name('articles.bulk');
    Route::get('/articles/{article}/edit', [AdminController::class, 'editArticle'])->middleware('can:manage_articles')->name('articles.edit');
    Route::put('/articles/{article}', [AdminController::class, 'updateArticle'])->middleware('can:manage_articles')->name('articles.update');
    Route::delete('/articles/{article}', [AdminController::class, 'deleteArticle'])->middleware('can:manage_articles')->name('articles.delete');
    Route::post('/articles/{articleId}/restore', [AdminController::class, 'restoreArticle'])->middleware('can:manage_articles')->name('articles.restore');
    Route::post('/articles/{article}/transition', [AdminController::class, 'transitionArticle'])->middleware('can:manage_articles')->name('articles.transition');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'createCategory'])->middleware('can:manage_categories')->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->middleware('can:manage_categories')->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->middleware('can:manage_categories')->name('categories.delete');
    Route::get('/tags', [AdminController::class, 'tags'])->name('tags');
    Route::post('/tags', [AdminController::class, 'createTag'])->middleware('can:manage_tags')->name('tags.store');
    Route::put('/tags/{tag}', [AdminController::class, 'updateTag'])->middleware('can:manage_tags')->name('tags.update');
    Route::delete('/tags/{tag}', [AdminController::class, 'deleteTag'])->middleware('can:manage_tags')->name('tags.delete');
    Route::post('/tags/{tag}/merge', [AdminController::class, 'mergeTag'])->middleware('can:manage_tags')->name('tags.merge');
    Route::get('/media', [AdminController::class, 'media'])->name('media');
    Route::post('/media', [AdminController::class, 'storeMedia'])->middleware('can:manage_media')->name('media.store');
    Route::patch('/media/{media}', [AdminController::class, 'updateMedia'])->middleware('can:manage_media')->name('media.update');
    Route::delete('/media/{media}', [AdminController::class, 'deleteMedia'])->middleware('can:manage_media')->name('media.delete');
    Route::get('/advertisements', [AdminController::class, 'advertisements'])->name('advertisements');
    Route::post('/advertisements', [AdminController::class, 'createAdvertisement'])->middleware('can:manage_ads')->name('advertisements.store');
    Route::put('/advertisements/{advertisement}', [AdminController::class, 'updateAdvertisement'])->middleware('can:manage_ads')->name('advertisements.update');
    Route::delete('/advertisements/{advertisement}', [AdminController::class, 'deleteAdvertisement'])->middleware('can:manage_ads')->name('advertisements.delete');
    Route::get('/homepage', [AdminController::class, 'homepage'])->name('homepage');
    Route::post('/homepage', [AdminController::class, 'saveHomepage'])->middleware('can:manage_homepage')->name('homepage.store');
    Route::put('/homepage/{section}', [AdminController::class, 'updateHomepageSection'])->middleware('can:manage_homepage')->name('homepage.update');
    Route::delete('/homepage/{section}', [AdminController::class, 'deleteHomepageSection'])->middleware('can:manage_homepage')->name('homepage.delete');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'saveSettings'])->middleware('can:manage_settings')->name('settings.store');
    Route::get('/seo', [AdminController::class, 'seo'])->name('seo');
    Route::post('/seo', [AdminController::class, 'saveSeo'])->middleware('can:manage_seo')->name('seo.store');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'createUser'])->middleware('can:manage_users')->name('users.store');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->middleware('can:manage_users')->name('users.update');
    Route::post('/users/{user}/deactivate', [AdminController::class, 'deactivateUser'])->middleware('can:manage_users')->name('users.deactivate');
    Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
    Route::get('/permissions', [AdminController::class, 'permissions'])->name('permissions');
    Route::get('/audit', [AdminController::class, 'audit'])->middleware('can:view_audit')->name('audit');
    Route::post('/articles/revisions/{revision}/restore', [AdminController::class, 'restoreArticleRevision'])->middleware('can:manage_articles')->name('articles.revisions.restore');
    Route::post('/roles', [AdminController::class, 'createRole'])->middleware('can:manage_roles')->name('roles.store');
    Route::put('/roles/{role}/permissions', [AdminController::class, 'updateRolePermissions'])->middleware('can:manage_roles')->name('roles.permissions');
});

require __DIR__.'/auth.php';
