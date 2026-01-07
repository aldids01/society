<?php

use App\Livewire\GuarantorHistory;
use App\Livewire\PendingLoans;
use App\Livewire\SavingHistory;
use App\Livewire\SavingUpdate;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('admin', 'dashboard-admin')
    ->middleware(['auth', 'verified'])
    ->name('dashboard.admin');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
    Route::get('loans', \App\Livewire\LoansTable::class)->name('loans.request');
    Route::get('loans-withdrawal', \App\Livewire\LoanWithdrawal::class)->name('loans.withdrawal');
    Route::get('pending-loans', PendingLoans::class)->name('loans.pending');
    Route::get('grains', \App\Livewire\GrainIndex::class)->name('grain.request');
    Route::get('pending-gains', \App\Livewire\PendingGrain::class)->name('grain.pending');
    Route::get('guarantors', \App\Livewire\GuarantorRequest::class)->name('guarantor.request');
    Route::get('guarantors-history', GuarantorHistory::class)->name('guarantor.history');
    Route::get('saving-update', SavingUpdate::class)->name('saving.update');
    Route::get('saving-history', SavingHistory::class)->name('saving.history');

    Route::prefix('admin')->group(function () {
        Route::get('deductions', \App\Livewire\DeductionsReport::class)->name('deductions.index');
        Route::get('loans', \App\Livewire\LoansReport::class)->name('loans.report');
        Route::get('grains', \App\Livewire\GrainsReport::class)->name('grains.report');
        Route::get('savings', \App\Livewire\SavingsReport::class)->name('savings.report');
        Route::get('members', \App\Livewire\MembersReport::class)->name('members.report');
        Route::get('new-member', \App\Livewire\NewMember::class)->name('members.create');
        Route::get('saving/{member:slug}', \App\Livewire\MemberSavings::class)->name('member.saving');
    });
});
