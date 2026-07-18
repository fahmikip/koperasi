<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingRequest;
use App\Models\Saving;
use App\Repositories\Contracts\SavingRepositoryInterface;
use App\Services\SavingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SavingController extends Controller
{
    public function __construct(private SavingRepositoryInterface $savings, private SavingService $service) {}

    public function index(): View
    {
        $this->authorize('viewAny', Saving::class);

        $filters = request()->only('search', 'direction', 'saving_type_id');

        return view('savings.index', [
            'savings' => $this->savings->paginate($filters),
            'types' => $this->savings->savingTypes(),
            'summary' => $this->savings->summary($filters),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Saving::class);

        return $this->form(new Saving);
    }

    public function store(SavingRequest $request): RedirectResponse
    {
        $saving = $this->service->create($request->validated(), $request->user()->id);

        return redirect()->route('savings.show', $saving)->with('success', 'Transaksi simpanan berhasil dicatat.');
    }

    public function show(Saving $saving): View
    {
        $this->authorize('view', $saving);
        $saving->load(['member', 'type', 'creator']);

        $balance = $this->savings->balance($saving->member_id, $saving->saving_type_id);

        return view('savings.show', compact('saving', 'balance'));
    }

    public function edit(Saving $saving): View
    {
        $this->authorize('update', $saving);

        return $this->form($saving);
    }

    public function update(SavingRequest $request, Saving $saving): RedirectResponse
    {
        $this->authorize('update', $saving);
        $this->service->update($saving, $request->validated());

        return redirect()->route('savings.show', $saving)->with('success', 'Transaksi simpanan berhasil diperbarui.');
    }

    public function destroy(Saving $saving): RedirectResponse
    {
        $this->authorize('delete', $saving);
        $this->service->delete($saving);

        return redirect()->route('savings.index')->with('success', 'Transaksi simpanan berhasil dihapus.');
    }

    private function form(Saving $saving): View
    {
        return view('savings.form', [
            'saving' => $saving,
            'members' => $this->savings->activeMembers(),
            'types' => $this->savings->savingTypes(activeOnly: true),
        ]);
    }
}
