<?php

namespace App\Filament\Widgets;

use App\Models\Saving;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SavingChart extends ChartWidget
{
    protected static ?string $maxHeight = '400px';
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Four Years Saving Comparison';

    protected function getData(): array
    {
            $currentYear = date('Y');
            $startYear = $currentYear - 3;
            $years = range($startYear, $currentYear);

            $datasets = [];

            $colors = [
                '#16A34A', '#1D4ED8', '#D97706', '#DC2626', '#7C3AED'
            ];


            foreach ($years as $index => $year) {

                $monthlySavings = Saving::where('annual', '=', $year)
                    ->where('status', 'active')
                    ->selectRaw('
                        SUM(January) as January,
                        SUM(February) as February,
                        SUM(March) as March,
                        SUM(April) as April,
                        SUM(May) as May,
                        SUM(June) as June,
                        SUM(July) as July,
                        SUM(August) as August,
                        SUM(September) as September,
                        SUM(October) as October,
                        SUM(November) as November,
                        SUM(December) as December
                    ')->first();


                $datasets[] = [
                    'label' => $year,
                    'data' => [
                        $monthlySavings->January,
                        $monthlySavings->February,
                        $monthlySavings->March,
                        $monthlySavings->April,
                        $monthlySavings->May,
                        $monthlySavings->June,
                        $monthlySavings->July,
                        $monthlySavings->August,
                        $monthlySavings->September,
                        $monthlySavings->October,
                        $monthlySavings->November,
                        $monthlySavings->December,
                    ],
                    'borderColor' => $colors[$index % count($colors)],
                    'backgroundColor' => $colors[$index % count($colors)] . '33',
                ];
            }

        return [
            'datasets' => $datasets,
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
