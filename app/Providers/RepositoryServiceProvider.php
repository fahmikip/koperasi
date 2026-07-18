<?php

namespace App\Providers;

use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\EloquentMemberRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MemberRepositoryInterface::class, EloquentMemberRepository::class);
    }
}
