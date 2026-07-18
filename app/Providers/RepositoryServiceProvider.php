<?php

namespace App\Providers;

use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\SavingRepositoryInterface;
use App\Repositories\EloquentMemberRepository;
use App\Repositories\EloquentSavingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MemberRepositoryInterface::class, EloquentMemberRepository::class);
        $this->app->bind(SavingRepositoryInterface::class, EloquentSavingRepository::class);
    }
}
