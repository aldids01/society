<?php

namespace App\Livewire;



use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SavingStatus extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    public array $tableColumnSearches = [];
    protected function getTablePage(): string
    {
        return SavingsReport::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Active Members', 'NGN' . number_format($this->getPageTableQuery()->where('status', 'active')->sum('total')))
                ->description('Total saving of active members')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Inactive Members', 'NGN' . number_format($this->getPageTableQuery()->where('status', 'inactive')->sum('total')))
                ->description('Total saving of inactive members')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
            Stat::make('Withdrawn Member', 'NGN' . number_format($this->getPageTableQuery()->where('status', 'withdrawn')->sum('total')))
                ->description('Total saving of withdrawn members')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('danger'),
        ];
    }
}
