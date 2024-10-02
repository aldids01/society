<?php

namespace App\Filament\Member\Widgets;

use App\Models\Saving;
use App\Models\LoanAmort;
use App\Models\GrainAmort;
use App\Models\Guarantor;
use Filament\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '120s';

    protected function getStats(): array
    {
        $sumAmount = Guarantor::where('guarantor_name', Auth::user()->applicant->staff_id)
        ->where('guarantor_status', 'approved')
        ->with(['loan.loanAmort' => function ($query) {
            $query->where('status', 'pending');
        }])
        ->get()
        ->pluck('loan.loanAmort')
        ->flatten()
        ->sum('principal');
        return [
            Stat::make('Guaranteed', number_format($sumAmount))
                ->description('Total Pending Guaranteed Loan ')
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger')
                ->url('/member/guarantors'),
            Stat::make('Loan', 'NGN '.number_format(LoanAmort::where('loan_owner', Auth::user()->applicant->staff_id)->where('status', 'pending')->sum('principal')))
                ->description('Total Pending Loan')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary')
                ->url('/member/loan-amorts'),
            Stat::make('Grain', 'NGN '.number_format(GrainAmort::where('grain_owner', Auth::user()->applicant->staff_id)->where('status', 'pending')->sum('principal')))
                ->description('Total Pending Grain')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('/member/grain-amorts')
                ->color('info'),
            Stat::make('Saving', 'NGN '.number_format(Saving::where('applicant_id', Auth::user()->applicant->staff_id)->sum('total')))
                ->description('Total Saving')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url('/member/savings')
                ->color('success'),
        ];
    }
}
