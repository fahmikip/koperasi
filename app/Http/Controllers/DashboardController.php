<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardRequest;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardRequest $request, DashboardService $dashboardService): View
    {
        $stats = ['members' => Member::count(), 'active' => Member::where('status', 'active')->count(), 'inactive' => Member::where('status', 'inactive')->count(), 'savings' => (float) Saving::selectRaw("COALESCE(SUM(CASE WHEN direction='deposit' THEN amount ELSE -amount END),0) total")->value('total'), 'loans' => (float) Loan::whereIn('status', ['approved', 'disbursed'])->sum('remaining_balance'), 'installments' => (float) Installment::sum('total_paid'), 'monthly_income' => (float) Installment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('interest_paid')];
        $recent = Saving::with('member', 'type')->latest()->limit(8)->get();
        $chart = $dashboardService->chart($request->validated());

        return view('dashboard', compact('stats', 'recent', 'chart'));
    }
}
