<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanDisbursementRequest;
use App\Http\Requests\LoanRequest;
use App\Http\Requests\LoanReviewRequest;
use App\Models\Loan;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private LoanRepositoryInterface $loans, private LoanService $service) {}

    public function index(): View
    {
        $this->authorize('viewAny', Loan::class);

        return view('loans.index', [
            'loans' => $this->loans->paginate(request()->only('search', 'status')),
            'summary' => $this->loans->summary(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Loan::class);

        return $this->form(new Loan);
    }

    public function store(LoanRequest $request): RedirectResponse
    {
        $loan = $this->service->submit($request->validated());

        return redirect()->route('loans.show', $loan)->with('success', 'Pengajuan pinjaman berhasil dibuat.');
    }

    public function show(Loan $loan): View
    {
        $this->authorize('view', $loan);

        return view('loans.show', ['loan' => $loan->load(['member', 'approver', 'installments'])]);
    }

    public function edit(Loan $loan): View
    {
        $this->authorize('update', $loan);

        return $this->form($loan);
    }

    public function update(LoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->authorize('update', $loan);
        $this->service->update($loan, $request->validated());

        return redirect()->route('loans.show', $loan)->with('success', 'Pengajuan pinjaman berhasil diperbarui.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->authorize('delete', $loan);
        $this->service->delete($loan);

        return redirect()->route('loans.index')->with('success', 'Pengajuan pinjaman berhasil dihapus.');
    }

    public function approve(LoanReviewRequest $request, Loan $loan): RedirectResponse
    {
        $this->authorize('review', $loan);
        $this->service->approve($loan, $request->user()->id, $request->validated('review_notes'));

        return back()->with('success', 'Pinjaman berhasil disetujui.');
    }

    public function reject(LoanReviewRequest $request, Loan $loan): RedirectResponse
    {
        $this->authorize('review', $loan);
        $this->service->reject($loan, $request->user()->id, $request->validated('review_notes'));

        return back()->with('success', 'Pengajuan pinjaman ditolak.');
    }

    public function disburse(LoanDisbursementRequest $request, Loan $loan): RedirectResponse
    {
        $this->authorize('disburse', $loan);
        $this->service->disburse($loan, $request->validated('disbursed_at'), $request->validated('disbursement_notes'));

        return back()->with('success', 'Pinjaman berhasil dicairkan.');
    }

    private function form(Loan $loan): View
    {
        return view('loans.form', ['loan' => $loan, 'members' => $this->loans->activeMembers()]);
    }
}
