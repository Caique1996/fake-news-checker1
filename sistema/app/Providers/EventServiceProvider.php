<?php

namespace App\Providers;

use App\Models\Api;
use App\Models\ImageSearch;
use App\Models\News;
use App\Models\Search;
use App\Models\User;
use App\Observers\ApiObserver;
use App\Observers\ImageSearchObserver;
use App\Observers\NewsObserver;
use App\Observers\SearchObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Api::observe(ApiObserver::class);
        User::observe(UserObserver::class);
        News::observe(NewsObserver::class);
        ImageSearch::observe(ImageSearchObserver::class);
        Search::observe(SearchObserver::class);

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
