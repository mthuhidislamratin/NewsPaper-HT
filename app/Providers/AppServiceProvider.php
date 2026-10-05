<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::define('access-admin', function (User $user) {
            return $user->is_active && $user->hasAnyRole([
                'Super Admin',
                'Admin',
                'Editor',
                'Author',
                'Journalist',
                'Reviewer',
                'SEO Manager',
                'Advertisement Manager',
                'Media Manager',
                'Moderator',
                'Viewer',
            ]);
        });
    }
}
