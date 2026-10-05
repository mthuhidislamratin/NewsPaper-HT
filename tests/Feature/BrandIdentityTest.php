<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_the_locked_jago24barta_identity_and_bangla_navigation(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('<html lang="bn">', false)
            ->assertSee('Jago24Barta | সত্যের সন্ধানে আপোষহীন', false)
            ->assertSee('জাগো২৪বার্তা', false)
            ->assertSee('সত্যের সন্ধানে আপোষহীন', false)
            ->assertSee('প্রচ্ছদ')
            ->assertSee('অনুসন্ধান')
            ->assertSee('og:site_name" content="Jago24Barta', false)
            ->assertSee(asset(config('jago24.logo')), false);

        $this->assertFileExists(public_path(config('jago24.logo')));
        $this->assertFileExists(public_path(config('jago24.favicon')));
    }

    public function test_public_information_pages_and_not_found_page_are_branded_in_bangla(): void
    {
        $this->get(route('pages.about'))
            ->assertOk()
            ->assertSee('আমাদের পরিচয়')
            ->assertSee('সত্যের সন্ধানে আপোষহীন');

        $this->get(route('pages.contact'))->assertOk()->assertSee('যোগাযোগ');
        $this->get(route('pages.newsletter'))->assertOk()->assertSee('সংবাদের সঙ্গে থাকুন');

        $this->get('/page-does-not-exist')
            ->assertNotFound()
            ->assertSee('এই পৃষ্ঠাটি খুঁজে পাওয়া যায়নি');
    }

    public function test_known_section_names_have_bangla_display_labels(): void
    {
        $category = new Category(['name' => 'National', 'slug' => 'national']);

        $this->assertSame('জাতীয়', $category->display_name);
    }

    public function test_placeholder_advertisements_are_not_published_to_the_public_site(): void
    {
        Advertisement::create([
            'name' => 'Legacy placeholder',
            'placement' => 'header',
            'title' => 'Sample ad',
            'image_url' => 'https://placehold.co/1200x150',
            'target_url' => 'https://example.test',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('placehold.co')
            ->assertDontSee('1200 × 150');

        $this->assertNull(Advertisement::query()->active()->first());
    }

    public function test_markdown_email_uses_the_jago24barta_logo_and_brand_theme(): void
    {
        $html = app(\Illuminate\Mail\Markdown::class)->render('mail::message', [
            'slot' => 'A newsroom notification.',
            'url' => route('home'),
        ])->toHtml();

        $this->assertSame('jago24', config('mail.markdown.theme'));
        $this->assertStringContainsString(asset(config('jago24.logo')), $html);
        $this->assertStringContainsString(config('jago24.slogan'), $html);
        $this->assertStringContainsString('A newsroom notification.', $html);
    }
}
