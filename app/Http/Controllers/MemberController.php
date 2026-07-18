<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private MemberRepositoryInterface $members, private MemberService $service) {}

    public function index(): View
    {
        $this->authorize('viewAny', Member::class);

        return view('members.index', ['members' => $this->members->paginate(request()->only('search', 'status'))]);
    }

    public function create(): View
    {
        $this->authorize('create', Member::class);

        return view('members.form', ['member' => new Member]);
    }

    public function store(MemberRequest $r): RedirectResponse
    {
        $m = $this->service->create($r->safe()->except('photo'), $r->file('photo'));

        return redirect()->route('members.show', $m)->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Member $member): View
    {
        $this->authorize('view', $member);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $this->authorize('update', $member);

        return view('members.form', compact('member'));
    }

    public function update(MemberRequest $r, Member $member): RedirectResponse
    {
        $this->service->update($member, $r->safe()->except('photo'), $r->file('photo'));

        return redirect()->route('members.show', $member)->with('success', 'Data anggota diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->authorize('delete', $member);
        $this->service->delete($member);

        return redirect()->route('members.index')->with('success', 'Anggota dinonaktifkan.');
    }
}
