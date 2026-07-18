<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class EloquentAuditLogRepository implements AuditLogRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Activity::query()->with(['causer', 'subject'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query->where('description', 'like', "%{$search}%")->orWhere('properties', 'like', "%{$search}%")))
            ->when($filters['event'] ?? null, function (Builder $query, string $event): Builder {
                return match ($event) {
                    'login', 'logout' => $query->where('description', $event),
                    'other' => $query->whereNull('event')->whereNotIn('description', ['login', 'logout']),
                    default => $query->where('event', $event),
                };
            })
            ->when($filters['subject_type'] ?? null, fn (Builder $query, string $type) => $query->where('subject_type', $type))
            ->when($filters['causer_id'] ?? null, fn (Builder $query, int|string $id) => $query->where('causer_type', User::class)->where('causer_id', $id))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('id')->paginate(20)->withQueryString();
    }

    public function summary(): array
    {
        return [
            'today' => Activity::query()->whereDate('created_at', today())->count(),
            'changes' => Activity::query()->whereIn('event', ['created', 'updated', 'deleted'])->count(),
            'logins' => Activity::query()->where('description', 'login')->count(),
            'total' => Activity::query()->count(),
        ];
    }

    public function users(): Collection
    {
        return User::query()->whereIn('id', Activity::query()->where('causer_type', User::class)->whereNotNull('causer_id')->select('causer_id'))->orderBy('name')->get(['id', 'name', 'email']);
    }
}
