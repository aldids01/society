<?php

namespace App\Filament\Resources\GrainAmorts\Widgets;

use App\Filament\Resources\GrainAmorts\Pages\ManageGrainAmorts;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class GrainReportStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    protected function getTablePage(): string
    {
        return ManageGrainAmorts::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Grains', Number::format($this->getPageTableQuery()->sum('principal'), 2)),
            Stat::make('Interests', Number::format($this->getPageTableQuery()->sum('interest'), 2)),
            Stat::make('Payments', Number::format($this->getPageTableQuery()->sum('payment'), 2)),
        ];
    }
}
