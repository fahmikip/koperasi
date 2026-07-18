<?php

namespace App\Providers;

use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\InstallmentRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Repositories\Contracts\SavingRepositoryInterface;
use App\Repositories\EloquentAuditLogRepository;
use App\Repositories\EloquentInstallmentRepository;
use App\Repositories\EloquentLoanRepository;
use App\Repositories\EloquentMemberRepository;
use App\Repositories\EloquentReportRepository;
use App\Repositories\EloquentSavingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuditLogRepositoryInterface::class, EloquentAuditLogRepository::class);
        $this->app->bind(InstallmentRepositoryInterface::class, EloquentInstallmentRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, EloquentMemberRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, EloquentLoanRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, EloquentReportRepository::class);
        $this->app->bind(SavingRepositoryInterface::class, EloquentSavingRepository::class);
    }
}
