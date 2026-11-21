<?php

namespace App\Providers;

use App\Models\User;
use App\Services\NavBadgeService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as IlluminateView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Gate::define('admin', fn (User $user): bool => $user->isAdmin());
        Gate::define('member', fn (User $user): bool => $user->isMember());
        Gate::define('active', fn (User $user): bool => $user->is_active);

        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(Str::lower($request->string('username')).'|'.$request->ip());
        });

        View::composer(['layouts.admin', 'layouts.member'], function (IlluminateView $view): void {
            $user = Auth::user();

            $view->with('navBadges', $user instanceof User
                ? app(NavBadgeService::class)->countsFor($user)
                : []);
        });
    }
}
