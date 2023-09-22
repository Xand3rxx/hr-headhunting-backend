<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\CandidateRepository;
use App\Interfaces\CandidateRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CandidateRepositoryInterface::class, CandidateRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
