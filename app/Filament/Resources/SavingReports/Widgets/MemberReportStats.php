<?php

namespace App\Filament\Resources\SavingReports\Widgets;

use App\Filament\Resources\SavingReports\Pages\ManageSavingReports;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MemberReportStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    protected ?string $pollingInterval = null;
    protected function getTablePage(): string
    {
        return ManageSavingReports::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Active Members', number_format($this->getPageTableQuery()->where('status', 'active')->sum('total')))
                ->description('Total saving of active members')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Inactive Members', number_format($this->getPageTableQuery()->where('status', 'inactive')->sum('total')))
                ->description('Total saving of inactive members')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
            Stat::make('Withdrawn Member', number_format($this->getPageTableQuery()->where('status', 'withdrawn')->sum('total')))
                ->description('Total saving of withdrawn members')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('danger'),
        ];
    }
}
