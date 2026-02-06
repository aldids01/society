<?php

namespace App\Filament\Resources\Members\Widgets;

use App\Filament\Resources\Members\Pages\ManageMembers;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MemberStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    protected bool $hasDeferredFilters = true;
    protected function getTablePage(): string
    {
        return ManageMembers::class;
    }
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Active Members', number_format($this->getPageTableQuery()->where('status', 'active')->count()))
                ->description('Total number of active members')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Inactive Members', number_format($this->getPageTableQuery()->where('status', 'inactive')->count()))
                ->description('Total number of inactive members')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
            Stat::make('Withdrawn Member',  number_format($this->getPageTableQuery()->where('status', 'withdrawn')->count()))
                ->description('Total number of withdrawn members')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('danger'),
        ];
    }
}
