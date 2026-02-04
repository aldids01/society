<?php

namespace App\Filament\Resources\SavingReports\Widgets;


use App\Filament\Resources\SavingReports\Pages\ManageSavingReports;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class QuarterlyReportStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    protected ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ManageSavingReports::class;
    }
    protected function getStats(): array
    {
        $sums = $this->getPageTableQuery()
            ->selectRaw('
            SUM(January + February + March) as q1,
            SUM(April + May + June) as q2,
            SUM(July + August + September) as q3,
            SUM(October + November + December) as q4
        ')
            ->first();
        return [
            Stat::make('1st Quarter', Number::format($sums->q1 ?? 0, 2))
                ->description('January, February, March'),
            Stat::make('2nd Quarter', Number::format($sums->q2 ?? 0, 2))
                ->description('April, May, June'),
            Stat::make('3rd Quarter', Number::format($sums->q3 ?? 0, 2))
                ->description('July, August, September'),
            Stat::make('4th Quarter', Number::format($sums->q4 ?? 0, 2))
                ->description('October, November, December'),
        ];
    }
}
