<?php

namespace App\Providers;

use App\Repositories\Contracts\ConsultantLeaveRepositoryInterface;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use App\Repositories\Contracts\SiteRepositoryInterface;
use App\Repositories\Contracts\SiteVisitRepositoryInterface;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use App\Repositories\Eloquent\ConsultantLeaveRepository;
use App\Repositories\Eloquent\ConsultantRepository;
use App\Repositories\Eloquent\DailyRecordRepository;
use App\Repositories\Eloquent\SiteRepository;
use App\Repositories\Eloquent\SiteVisitRepository;
use App\Repositories\Eloquent\TaskDefinitionRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\WorkScheduleRepository;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Repositories\Eloquent\ReportRepository;
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
        $this->app->bind(TaskDefinitionRepositoryInterface::class, TaskDefinitionRepository::class);
        $this->app->bind(DailyRecordRepositoryInterface::class, DailyRecordRepository::class);
        $this->app->bind(SiteVisitRepositoryInterface::class, SiteVisitRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
