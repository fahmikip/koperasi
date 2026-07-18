<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/verify-member/{token}', [MemberCardController::class, 'verify'])->name('members.verify');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('members', MemberController::class);
    Route::resource('savings', SavingController::class);
    Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');
    Route::resource('loans', LoanController::class);
    Route::get('installments', [InstallmentController::class, 'index'])->name('installments.index');
    Route::get('loans/{loan}/installments/create', [InstallmentController::class, 'create'])->name('installments.create');
    Route::post('loans/{loan}/installments', [InstallmentController::class, 'store'])->name('installments.store');
    Route::get('installments/{installment}', [InstallmentController::class, 'show'])->name('installments.show');
    Route::get('reports/{type}/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('reports/{type}/excel', [ReportController::class, 'excel'])->name('reports.excel');
    Route::get('reports/{type?}', [ReportController::class, 'index'])->name('reports.index');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('audit-logs/{activity}', [AuditLogController::class, 'show'])->name('audit.show');
    Route::get('members/{member}/card', [MemberCardController::class, 'preview'])->name('members.card');
    Route::get('members/{member}/card/download', [MemberCardController::class, 'download'])->name('members.card.download');
});

require __DIR__.'/auth.php';
