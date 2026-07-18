<?php

namespace App\Http\Controllers;

use App\Models\CardPrintHistory;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MemberCardController extends Controller
{
    public function verify(string $token): View
    {
        $member = Member::where('qr_token', $token)->firstOrFail();

        return view('members.verify', compact('member'));
    }

    public function preview(Member $member): View
    {
        $this->authorize('view', $member);

        return view('members.card', ['member' => $member, 'isPdf' => false]);
    }

    public function download(Member $member): Response
    {
        $this->authorize('view', $member);
        CardPrintHistory::create(['member_id' => $member->id, 'printed_by' => auth()->id(), 'quantity' => 1, 'action' => 'download', 'printed_at' => now()]);
        activity()->performedOn($member)->causedBy(auth()->user())->log('download kartu anggota');

        return Pdf::loadView('members.card', ['member' => $member, 'isPdf' => true])->setPaper([0, 0, 242.65, 153.01])->download("kartu-{$member->member_number}.pdf");
    }
}
