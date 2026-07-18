<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallmentRequest;
use App\Models\Installment;
use App\Models\Loan;
use App\Repositories\Contracts\InstallmentRepositoryInterface;
use App\Services\InstallmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstallmentController extends Controller
{
    public function __construct(private InstallmentRepositoryInterface $installments, private InstallmentService $service) {}

    public function index(): View
    {
        $this->authorize('viewAny', Installment::class);

        return view('installments.index', ['installments' => $this->installments->paginate(request()->only('search', 'date_from', 'date_to'))]);
    }

    public function create(Loan $loan): View
    {
        $this->authorize('create', [Installment::class, $loan]);
        $loan->load('member');
        $paidInterest = (float) $this->installments->paidInterest($loan->id);
        $remainingInterest = max(0, (float) $loan->total_interest - $paidInterest);

        return view('installments.form', [
            'loan' => $loan,
            'nextNumber' => $this->installments->nextInstallmentNumber($loan->id),
            'remainingInterest' => $remainingInterest,
            'suggestedPrincipal' => min((float) $loan->remaining_balance, round((float) $loan->principal_amount / $loan->term_months, 2)),
            'suggestedInterest' => min($remainingInterest, round((float) $loan->total_interest / $loan->term_months, 2)),
        ]);
    }

    public function store(InstallmentRequest $request, Loan $loan): RedirectResponse
    {
        $this->authorize('create', [Installment::class, $loan]);
        $installment = $this->service->pay($loan, $request->validated(), $request->user()->id);

        return redirect()->route('installments.show', $installment)->with('success', 'Pembayaran angsuran berhasil dicatat.');
    }

    public function show(Installment $installment): View
    {
        $this->authorize('view', $installment);

        return view('installments.show', ['installment' => $installment->load(['loan.member', 'receiver'])]);
    }
}
