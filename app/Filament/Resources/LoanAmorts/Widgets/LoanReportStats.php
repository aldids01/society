<?php

namespace App\Filament\Resources\LoanAmorts\Widgets;

use App\Filament\Resources\LoanAmorts\LoanAmortResource;
use App\Filament\Resources\LoanAmorts\Pages\ManageLoanAmorts;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class LoanReportStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    protected ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ManageLoanAmorts::class;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Loans', Number::format($this->getPageTableQuery()->sum('principal'), 2)),
            Stat::make('Interests', Number::format($this->getPageTableQuery()->sum('interest'), 2)),
            Stat::make('Payments', Number::format($this->getPageTableQuery()->sum('payment'), 2)),
        ];
    }
}
