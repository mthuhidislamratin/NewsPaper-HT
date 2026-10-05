<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NewsroomWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Journalist', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_articles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_categories', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_tags', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_seo', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view_audit', 'guard_name' => 'web']);
    }

    public function test_guest_is_redirected_from_newsroom(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_newsroom_pages_render(): void
    {
        $admin = $this->userWithRoleAndPermission('Admin', 'manage_articles');
        $role = Role::findByName('Admin', 'web');
        $role->givePermissionTo('view_audit');
        $admin->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.articles', ['status' => 'draft']), false);

        foreach ([
            'admin.articles',
            'admin.categories',
            'admin.tags',
            'admin.media',
            'admin.advertisements',
            'admin.homepage',
            'admin.settings',
            'admin.seo',
            'admin.users',
            'admin.roles',
            'admin.permissions',
            'admin.audit',
        ] as $routeName) {
            $this->actingAs($admin)->get(route($routeName))->assertOk();
        }
    }

    public function test_active_account_without_newsroom_role_is_forbidden(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_create_and_publish_an_article_visible_on_the_public_site(): void
    {
        $admin = $this->userWithRoleAndPermission('Admin', 'manage_articles');
        $category = Category::create(['name' => 'World', 'slug' => 'world', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'A newsroom workflow test',
            'category_id' => $category->id,
            'content' => 'A published story body for the acceptance test.',
            'status' => 'published',
        ])->assertRedirect(route('admin.articles'));

        $article = Article::query()->where('title', 'A newsroom workflow test')->firstOrFail();
        $this->assertSame('published', $article->status);
        $this->assertTrue($article->is_published);
        $this->get(route('article.show', $article))->assertOk()->assertSee('A published story body for the acceptance test.');
    }

    public function test_draft_articles_are_not_publicly_accessible(): void
    {
        $article = Article::create([
            'title' => 'Unpublished newsroom draft',
            'slug' => 'unpublished-newsroom-draft',
            'content' => 'Private draft copy.',
            'status' => 'draft',
            'is_published' => false,
        ]);

        $this->get(route('article.show', $article))->assertNotFound();
    }

    public function test_public_search_filters_published_stories_by_section(): void
    {
        $selectedCategory = Category::create(['name' => 'World', 'slug' => 'world-search', 'is_active' => true]);
        $otherCategory = Category::create(['name' => 'Business', 'slug' => 'business-search', 'is_active' => true]);
        $selected = Article::create([
            'category_id' => $selectedCategory->id,
            'title' => 'Local search result',
            'slug' => 'local-search-result',
            'content' => 'A published result.',
            'status' => 'published',
            'is_published' => true,
            'published_at' => now(),
        ]);
        Article::create([
            'category_id' => $otherCategory->id,
            'title' => 'Local story in another section',
            'slug' => 'local-story-other-section',
            'content' => 'A different published result.',
            'status' => 'published',
            'is_published' => true,
            'published_at' => now(),
        ]);
        Article::create([
            'category_id' => $selectedCategory->id,
            'title' => 'Local unpublished result',
            'slug' => 'local-unpublished-result',
            'content' => 'A private draft.',
            'status' => 'draft',
            'is_published' => false,
        ]);

        $this->get(route('search', ['q' => 'Local', 'category_id' => $selectedCategory->id]))
            ->assertOk()
            ->assertSee($selected->title)
            ->assertDontSee('Local story in another section')
            ->assertDontSee('Local unpublished result');
    }

    public function test_homepage_seo_settings_are_rendered_in_public_metadata(): void
    {
        $admin = $this->userWithRoleAndPermission('Admin', 'manage_seo');

        $this->actingAs($admin)->post(route('admin.seo.store'), [
            'page' => 'home',
            'meta_title' => 'Newsroom SEO test title',
            'meta_description' => 'Newsroom SEO test description.',
            'canonical_url' => route('home'),
        ])->assertRedirect(route('admin.seo'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Newsroom SEO test title</title>', false)
            ->assertSee('<meta name="description" content="Newsroom SEO test description.">', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false);
    }

    public function test_general_settings_cannot_change_the_locked_brand_name_or_slogan(): void
    {
        Permission::firstOrCreate(['name' => 'manage_settings', 'guard_name' => 'web']);
        $admin = $this->userWithRoleAndPermission('Admin', 'manage_settings');

        $this->actingAs($admin)->post(route('admin.settings.store'), [
            'site_name' => 'Another publication',
            'site_tagline' => 'Another slogan',
            'site_email' => 'desk@jago24barta.test',
            'default_timezone' => 'Asia/Dhaka',
        ])->assertRedirect(route('admin.settings'));

        $this->assertDatabaseMissing('site_settings', ['key' => 'site_name']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'site_tagline']);
        $this->assertDatabaseHas('site_settings', ['key' => 'site_email', 'value' => 'desk@jago24barta.test']);
    }

    public function test_category_and_tag_seo_fields_are_saved_and_rendered(): void
    {
        $admin = $this->userWithRoleAndPermission('Admin', 'manage_categories');
        Role::findByName('Admin', 'web')->givePermissionTo('manage_tags');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'SEO World',
            'slug' => 'seo-world',
            'seo_title' => 'World desk title',
            'meta_description' => 'World desk description.',
            'canonical_url' => 'https://news.example.test/world',
        ])->assertRedirect(route('admin.categories'));

        $this->post(route('admin.tags.store'), [
            'name' => 'SEO Topic',
            'slug' => 'seo-topic',
            'seo_title' => 'Topic page title',
            'meta_description' => 'Topic page description.',
            'canonical_url' => 'https://news.example.test/topic',
        ])->assertRedirect(route('admin.tags'));

        $this->get(route('category.show', 'seo-world'))
            ->assertOk()
            ->assertSee('<title>World desk title</title>', false)
            ->assertSee('https://news.example.test/world', false);
        $this->get(route('tag.show', 'seo-topic'))
            ->assertOk()
            ->assertSee('<title>Topic page title</title>', false)
            ->assertSee('https://news.example.test/topic', false);
    }

    public function test_merging_tags_moves_article_links_and_preserves_old_public_url(): void
    {
        $admin = $this->userWithRoleAndPermission('Admin', 'manage_tags');
        $source = \App\Models\Tag::create(['name' => 'Old topic', 'slug' => 'old-topic', 'is_active' => true]);
        $target = \App\Models\Tag::create(['name' => 'Current topic', 'slug' => 'current-topic', 'is_active' => true]);
        $article = Article::create([
            'title' => 'Tagged search story',
            'slug' => 'tagged-search-story',
            'content' => 'A story linked to an old topic.',
            'status' => 'published',
            'is_published' => true,
            'published_at' => now(),
        ]);
        $article->tags()->attach($source);

        $this->actingAs($admin)
            ->post(route('admin.tags.merge', $source), ['target_tag_id' => $target->id])
            ->assertRedirect(route('admin.tags'));

        $this->assertDatabaseHas('article_tag', ['article_id' => $article->id, 'tag_id' => $target->id]);
        $this->assertSoftDeleted('tags', ['id' => $source->id]);
        $this->get(route('tag.show', 'old-topic'))->assertRedirect(route('tag.show', ['slug' => $target->slug]));
    }

    public function test_search_rejects_invalid_date_ranges(): void
    {
        $this->get(route('search', ['q' => 'news', 'from' => 'not-a-date']))
            ->assertSessionHasErrors('from');
    }

    public function test_editor_can_restore_a_trashed_article_and_the_action_is_audited(): void
    {
        $admin = $this->userWithRoleAndPermission('Admin', 'manage_articles');
        $article = Article::create([
            'title' => 'Restorable story',
            'slug' => 'restorable-story',
            'content' => 'A story that was moved to trash.',
            'status' => 'draft',
            'is_published' => false,
        ]);
        $article->delete();

        $this->actingAs($admin)
            ->post(route('admin.articles.restore', $article->id))
            ->assertRedirect(route('admin.articles'));

        $this->assertFalse($article->fresh()->trashed());
        $this->assertDatabaseHas('newsroom_audit_events', [
            'action' => 'article.restored',
            'subject_type' => 'Article',
            'subject_id' => $article->id,
        ]);

        $trashedArticle = Article::create([
            'title' => 'Trash listing story',
            'slug' => 'trash-listing-story',
            'content' => 'A story shown in the trash listing.',
            'status' => 'draft',
            'is_published' => false,
        ]);
        $trashedArticle->delete();
        $this->get(route('admin.articles', ['status' => 'trashed']))
            ->assertOk()
            ->assertSee('Trash listing story')
            ->assertSee('Restore');
    }

    public function test_journalist_can_submit_work_for_review_but_cannot_publish(): void
    {
        $journalist = $this->userWithRoleAndPermission('Journalist', 'manage_articles');

        $this->actingAs($journalist)->post(route('admin.articles.store'), [
            'title' => 'Submitted for review',
            'content' => 'This should remain in the review queue.',
            'status' => 'review',
        ])->assertRedirect(route('admin.articles'));

        $this->assertDatabaseHas('articles', ['title' => 'Submitted for review', 'status' => 'review']);

        $this->actingAs($journalist)->post(route('admin.articles.store'), [
            'title' => 'Unauthorized publication attempt',
            'content' => 'A journalist cannot publish directly.',
            'status' => 'published',
        ])->assertForbidden();
    }

    public function test_editorial_workflow_schedules_and_publishes_an_article(): void
    {
        $editor = $this->userWithRoleAndPermission('Editor', 'manage_articles');
        $article = Article::create([
            'author_id' => $editor->id,
            'title' => 'Scheduled workflow story',
            'slug' => 'scheduled-workflow-story',
            'content' => 'Scheduled article body.',
            'status' => 'review',
            'is_published' => false,
        ]);

        $this->actingAs($editor)
            ->post(route('admin.articles.transition', $article), ['status' => 'fact_check'])
            ->assertSessionHasNoErrors();
        $this->post(route('admin.articles.transition', $article), ['status' => 'approved'])
            ->assertSessionHasNoErrors();

        $publicationTime = now()->addMinutes(2);
        $this->post(route('admin.articles.transition', $article), [
            'status' => 'scheduled',
            'scheduled_at' => $publicationTime->format('Y-m-d H:i:s'),
        ])->assertSessionHasNoErrors();

        $this->travelTo($publicationTime->addMinute());
        $this->artisan('news:publish-scheduled')->assertExitCode(0);
        $this->travelBack();

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'status' => 'published',
            'is_published' => true,
        ]);
        $this->get(route('article.show', $article))->assertOk()->assertSee('Scheduled article body.');
    }

    private function userWithRoleAndPermission(string $roleName, string $permissionName): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $role = Role::findByName($roleName, 'web');
        $role->givePermissionTo($permissionName);
        $user->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }
}
