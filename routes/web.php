<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
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
    Route::get('members/{member}/card', [MemberCardController::class, 'preview'])->name('members.card');
    Route::get('members/{member}/card/download', [MemberCardController::class, 'download'])->name('members.card.download');
});

require __DIR__.'/auth.php';
