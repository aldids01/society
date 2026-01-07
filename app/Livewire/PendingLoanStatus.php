<?php

namespace App\Livewire;



use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingLoanStatus extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    public array $tableColumnSearches = [];
    protected function getTablePage(): string
    {
        return LoansReport::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Interest', 'NGN ' . number_format($this->getPageTableQuery()->sum('interest')))
                ->description('Total Interest')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            Stat::make('Loan', 'NGN' . number_format($this->getPageTableQuery()->sum('principal')))
                ->description('Total Loan')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
            Stat::make('Payment', 'NGN' . number_format($this->getPageTableQuery()->sum('payment')))
                ->description('Total Payment')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('danger'),
        ];
    }
}
