<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Project;
use App\Task;
// use App\Observers\ProjectObserver;
// use App\Observers\TaskObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Project::observe(ProjectObserver::class);
        // Task::observe(TaskObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
