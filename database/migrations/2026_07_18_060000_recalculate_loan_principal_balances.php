<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('loans') || ! Schema::hasTable('installments')) {
            return;
        }

        DB::table('loans')->whereIn('status', ['disbursed', 'paid'])->orderBy('id')->each(function (object $loan): void {
            $principalPaid = (float) DB::table('installments')->where('loan_id', $loan->id)->sum('principal_paid');
            DB::table('loans')->where('id', $loan->id)->update([
                'remaining_balance' => max(0, (float) $loan->principal_amount - $principalPaid),
            ]);
        });
    }

    public function down(): void
    {
        // The previous mixed principal-and-interest balance cannot be reconstructed safely.
    }
};
