<?php

namespace App\Providers;

use App\Repositories\Contracts\ConsultantLeaveRepositoryInterface;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\SiteRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use App\Repositories\Eloquent\ConsultantLeaveRepository;
use App\Repositories\Eloquent\ConsultantRepository;
use App\Repositories\Eloquent\SiteRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\WorkScheduleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SiteRepositoryInterface::class, SiteRepository::class);
        $this->app->bind(ConsultantRepositoryInterface::class, ConsultantRepository::class);
        $this->app->bind(WorkScheduleRepositoryInterface::class, WorkScheduleRepository::class);
        $this->app->bind(ConsultantLeaveRepositoryInterface::class, ConsultantLeaveRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
