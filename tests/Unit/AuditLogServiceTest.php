<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Services\AuditLogService;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AuditLogServiceTest extends TestCase
{
    public function test_labels_and_values_are_human_readable(): void
    {
        $service = new AuditLogService;
        $activity = new Activity;
        $activity->setRawAttributes(['description' => 'login', 'event' => null]);

        $this->assertSame('Masuk Sistem', $service->eventLabel($activity));
        $this->assertSame('Anggota', $service->subjectLabel(Member::class));
        $this->assertSame('Nama Lengkap', $service->fieldLabel('nama_lengkap'));
        $this->assertSame('Ya', $service->displayValue(true));
        $this->assertSame('-', $service->displayValue(null));
    }
}
