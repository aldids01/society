<?php

namespace App\Livewire;


use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingGrainStatus extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    public array $tableColumnSearches = [];
    protected function getTablePage(): string
    {
        return \App\Livewire\GrainsReport::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Interest', 'NGN ' . number_format($this->getPageTableQuery()->sum('interest')))
                ->description('Total Interest')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            Stat::make('Grain', 'NGN' . number_format($this->getPageTableQuery()->sum('principal')))
                ->description('Total Grain')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Payment', 'NGN' . number_format($this->getPageTableQuery()->sum('payment')))
                ->description('Total Payment')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('danger'),
        ];
    }
}
