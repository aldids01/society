<?php

namespace App\Livewire;

use App\Models\Grain;
use App\Models\GrainAmort;
use App\Models\Guarantor;
use App\Models\Loan;
use App\Models\LoanAmort;
use App\Models\Saving;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoanStatus extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        $user = auth()->user()->name;
        return "$user Analytics";
    }

    protected function getDescription(): ?string
    {
        $user = auth()->user()->name;
        return "An overview of $user Financial Analytics";
    }

    protected function getStats(): array
    {
        $loan = LoanAmort::where('member_id', auth()->user()->member->slug)->where('status', '!=', 'paid')->where('status', '!=', 'complete')->first();
        if ($loan) {
            $loanid = Loan::where('slug', $loan->loan_id)->first();
        } else {
            $loanid = null; // Set $loanid to null if no LoanAmort record is found
        }

        $grain = GrainAmort::where('member_id', auth()->user()->member->slug)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->first();
        if ($grain) {
            $grain_id = Grain::where('slug', $grain->grain_id)->first();
        } else {
            $grain_id = null; // Set $loanid to null if no LoanAmort record is found
        }
        $sumAmount = Guarantor::where('member_id', auth()->user()->member->slug)
            ->where('status', 'approved')
            ->whereHas('loan.loanAmorts', function ($query) {
                $query->where('status', '!=', 'paid')->where('status', '!=', 'complete');
            })
            ->get()
            ->pluck('loan.loanAmorts')
            ->flatten()
            ->sum('principal');
        return [
            Stat::make('Guaranteed', 'NGN' . number_format($sumAmount))
                ->description('Total Pending Guaranteed Loan ')
                ->descriptionIcon('heroicon-m-users')
                ->color('danger')
                ->url(route('guarantor.history')),
            Stat::make('Loan', 'NGN' . number_format(LoanAmort::query()->where('member_id', auth()->user()->member->slug)->where('status', '!=', 'paid')->where('status', '!=', 'complete')->sum('principal')))
                ->description('Total Pending Loan')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary')
                ->url(route('loans.pending')),
            Stat::make('Grain', 'NGN ' . number_format(GrainAmort::query()->where('member_id', auth()->user()->member->slug)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->sum('principal')))
                ->description('Total Pending Grain')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->url(route('grain.request'))
                ->color('info'),
            Stat::make('Saving', 'NGN' . number_format(Saving::query()->where('member_id', auth()->user()->member->slug)->sum('total')))
                ->description('Total Saving')
                ->descriptionIcon('heroicon-m-banknotes')
                ->url(route('saving.history'))
                ->color('success'),
        ];
    }
}
