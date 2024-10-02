<?php

namespace App\Filament\Widgets;

use App\Models\Saving;
use App\Models\Guarantor;
use App\Models\LoanAmort;
use App\Models\GrainAmort;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        
        return [
            Stat::make('Loan', 'NGN '.number_format(LoanAmort::where('status', 'pending')->sum('principal')))
                ->description('Total Pending Loan')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
            Stat::make('Grain', 'NGN '.number_format(GrainAmort::where('status', 'pending')->sum('principal')))
                ->description('Total Pending Grain')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),
            Stat::make('Saving', 'NGN '.number_format(Saving::where('status', 'active')->sum('total')))
                ->description('Total Saving for Active Members')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
