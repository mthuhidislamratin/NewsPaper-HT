<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Editor',
            'Journalist',
            'Reviewer',
            'Author',
            'SEO Manager',
            'Advertisement Manager',
            'Media Manager',
            'Moderator',
            'Viewer',
        ];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $permissions = [
            'manage_articles',
            'manage_categories',
            'manage_tags',
            'manage_media',
            'manage_ads',
            'manage_homepage',
            'manage_seo',
            'manage_settings',
            'manage_users',
            'manage_roles',
            'manage_comments',
            'view_analytics',
            'view_audit',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $permissionMap = [
            'Admin' => array_values(array_diff($permissions, ['manage_roles'])),
            'Editor' => ['manage_articles', 'manage_categories', 'manage_tags', 'manage_media', 'manage_homepage', 'manage_seo'],
            'Journalist' => ['manage_articles'],
            'Reviewer' => ['manage_articles'],
            'Author' => ['manage_articles'],
            'SEO Manager' => ['manage_seo'],
            'Advertisement Manager' => ['manage_ads'],
            'Media Manager' => ['manage_media'],
            'Moderator' => ['manage_comments'],
            'Viewer' => ['view_analytics', 'view_audit'],
        ];

        foreach ($permissionMap as $roleName => $rolePermissions) {
            Role::findByName($roleName, 'web')->syncPermissions($rolePermissions);
        }

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@jago24barta.local'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $superAdmin->syncRoles(['Super Admin']);
        $superAdmin->givePermissionTo($permissions);

        Category::firstOrCreate(['slug' => 'politics'], ['name' => 'রাজনীতি', 'description' => 'রাজনীতি বিষয়ক সংবাদ ও বিশ্লেষণ', 'is_active' => true, 'sort_order' => 1]);
        Category::firstOrCreate(['slug' => 'sports'], ['name' => 'খেলাধুলা', 'description' => 'খেলাধুলার সংবাদ ও প্রতিবেদন', 'is_active' => true, 'sort_order' => 2]);
        Category::firstOrCreate(['slug' => 'business'], ['name' => 'বাণিজ্য', 'description' => 'বাণিজ্য ও অর্থনীতির সংবাদ', 'is_active' => true, 'sort_order' => 3]);

        Tag::firstOrCreate(['slug' => 'bangladesh'], ['name' => 'বাংলাদেশ', 'description' => 'বাংলাদেশ বিষয়ক সংবাদ', 'is_active' => true, 'sort_order' => 1]);
        Tag::firstOrCreate(['slug' => 'economy'], ['name' => 'অর্থনীতি', 'description' => 'অর্থনীতি বিষয়ক সংবাদ', 'is_active' => true, 'sort_order' => 2]);

        Advertisement::updateOrCreate(['placement' => 'header', 'name' => 'Header Banner'], [
            'title' => null,
            'content' => null,
            'target_url' => null,
            'image_url' => null,
            'is_active' => false,
            'sort_order' => 1,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);

        Advertisement::updateOrCreate(['placement' => 'sidebar', 'name' => 'Sidebar Ad'], [
            'title' => null,
            'content' => null,
            'target_url' => null,
            'image_url' => null,
            'is_active' => false,
            'sort_order' => 2,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);

        HomepageSection::firstOrCreate(['key' => 'breaking_news'], ['title' => 'ব্রেকিং নিউজ', 'config' => ['count' => 5], 'sort_order' => 1, 'is_active' => true]);
        HomepageSection::firstOrCreate(['key' => 'featured_stories'], ['title' => 'নির্বাচিত সংবাদ', 'config' => ['count' => 4], 'sort_order' => 2, 'is_active' => true]);
        HomepageSection::firstOrCreate(['key' => 'latest_news'], ['title' => 'সর্বশেষ সংবাদ', 'config' => ['count' => 8], 'sort_order' => 3, 'is_active' => true]);

        SiteSetting::updateOrCreate(['key' => 'site_name'], ['value' => 'Jago24Barta', 'type' => 'string']);
        SiteSetting::updateOrCreate(['key' => 'site_tagline'], ['value' => 'সত্যের সন্ধানে আপোষহীন', 'type' => 'string']);
        SiteSetting::updateOrCreate(['key' => 'site_email'], ['value' => env('NEWSROOM_CONTACT_EMAIL'), 'type' => 'string']);
        SiteSetting::updateOrCreate(['key' => 'meta_title'], ['value' => 'Jago24Barta', 'type' => 'string']);
        SiteSetting::updateOrCreate(['key' => 'meta_description'], ['value' => 'জাগো২৪বার্তা | সত্যের সন্ধানে আপোষহীন', 'type' => 'string']);
    }
}
