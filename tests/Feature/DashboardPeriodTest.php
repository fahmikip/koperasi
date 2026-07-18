<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPeriodTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('email', 'admin@koperasi.test')->firstOrFail();
    }

    public function test_dashboard_displays_financial_chart_with_default_period(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Analitik Periode')
            ->assertSee('Pergerakan Keuangan')
            ->assertSee('financial-period-chart');
    }

    public function test_dashboard_accepts_custom_period(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard', [
            'period' => 'custom',
            'date_from' => '2026-07-01',
            'date_to' => '2026-07-18',
        ]))->assertOk()->assertSee('01 Jul 2026')->assertSee('18 Jul 2026');
    }

    public function test_custom_period_rejects_invalid_date_range(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard', [
            'period' => 'custom',
            'date_from' => '2026-07-18',
            'date_to' => '2026-07-01',
        ]))->assertSessionHasErrors('date_to');
    }
}
