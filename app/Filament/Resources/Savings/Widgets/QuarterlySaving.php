<?php

namespace App\Filament\Resources\Savings\Widgets;

use App\Filament\Resources\LoanAmorts\Pages\ManageLoanAmorts;
use App\Filament\Resources\Savings\Pages\ManageSavings;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class QuarterlySaving extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    protected ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ManageSavings::class;
    }
    protected function getStats(): array
    {
        $sums = $this->getPageTableQuery()
            ->reorder()
            ->selectRaw('
            SUM(January + February + March) as q1,
            SUM(April + May + June) as q2,
            SUM(July + August + September) as q3,
            SUM(October + November + December) as q4
        ')
            ->first();
        return [
            Stat::make('1st Quarter', Number::format($sums->q1 ?? 0, 2)),
            Stat::make('2nd Quarter', Number::format($sums->q2 ?? 0, 2)),
            Stat::make('3rd Quarter', Number::format($sums->q3 ?? 0, 2)),
            Stat::make('4th Quarter', Number::format($sums->q4 ?? 0, 2))
        ];
    }
}
