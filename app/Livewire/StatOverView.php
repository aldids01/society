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

class StatOverView extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return config('app.name')." Analytics";
    }

    protected function getDescription(): ?string
    {
        $user = config('app.name');
        return "An overview of $user Financial Analytics";
    }

    protected function getStats(): array
    {
        $loan = LoanAmort::query()->where('status', '!=', 'paid')->where('status', '!=', 'complete')->first();
        if ($loan) {
            $loanid = Loan::where('slug', $loan->loan_id)->first();
        } else {
            $loanid = null; // Set $loanid to null if no LoanAmort record is found
        }

        $grain = GrainAmort::query()->where('status', '!=', 'paid')->where('status', '!=', 'completed')->first();
        if ($grain) {
            $grain_id = Grain::where('slug', $grain->grain_id)->first();
        } else {
            $grain_id = null; // Set $loanid to null if no LoanAmort record is found
        }
        return [
            Stat::make('Loan', 'NGN' . number_format(LoanAmort::query()->where('status', '!=', 'paid')->where('status', '!=', 'complete')->sum('principal')))
                ->description('Total Pending Loan')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary')
                ->url(route('loans.report')),
            Stat::make('Grain', 'NGN ' . number_format(GrainAmort::query()->where('status', '!=', 'paid')->where('status', '!=', 'completed')->sum('principal')))
                ->description('Total Pending Grain')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->url(route('grains.report'))
                ->color('info'),
            Stat::make('Saving', 'NGN' . number_format(Saving::where('status', '=', 'active')->sum('total')))
                ->description('Total Saving')
                ->descriptionIcon('heroicon-m-banknotes')
                ->url(route('savings.report'))
                ->color('success'),
        ];
    }
}
