<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogRequest;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function __construct(private AuditLogRepositoryInterface $activities, private AuditLogService $service) {}

    public function index(AuditLogRequest $request): View
    {
        return view('audit.index', [
            'activities' => $this->activities->paginate($request->validated()),
            'summary' => $this->activities->summary(),
            'users' => $this->activities->users(),
            'service' => $this->service,
        ]);
    }

    public function show(Activity $activity): View
    {
        Gate::authorize('audit.view');
        $activity->load(['causer', 'subject']);

        return view('audit.show', ['activity' => $activity, 'service' => $this->service, 'changes' => $this->service->changes($activity)]);
    }
}
