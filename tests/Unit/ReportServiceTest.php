<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Services\ReportService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ReportServiceTest extends TestCase
{
    public function test_member_report_builds_rows_and_summary(): void
    {
        $member = new Member;
        $member->setRawAttributes(['member_number' => 'KOP-001', 'name' => 'Budi', 'nik' => '1234567890123456', 'whatsapp' => '08123456789', 'joined_at' => Carbon::parse('2026-01-01'), 'valid_until' => Carbon::parse('2031-01-01'), 'status' => 'active']);
        $repository = $this->createMock(ReportRepositoryInterface::class);
        $repository->expects($this->once())->method('members')->willReturn(collect([
            $member,
        ]));

        $report = (new ReportService($repository))->generate('members', []);

        $this->assertSame('Laporan Anggota', $report['title']);
        $this->assertCount(1, $report['rows']);
        $this->assertSame(['Total Anggota', '1'], $report['summary'][0]);
    }
}
