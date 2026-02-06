<?php

namespace App\Filament\Resources\Savings\Widgets;

use App\Models\Saving;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Widgets\ChartWidget;

class SavingChart extends ChartWidget
{
    use HasPageShield;
    protected ?string $heading = 'Saving Chart';
    protected bool $hasDeferredFilters = true;
    protected bool $isCollapsible = true;
    protected ?string $maxHeight = '300px';
    protected static ?int $sort = 5;
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->hasRole('super_admin');
    }
    public function getHeading(): ?string
    {
        $user = config('app.name');
        return "$user Six Years Saving Comparison Analytics";
    }

    public function getDescription(): ?string
    {
        $user = config('app.name');
        return "An overview of $user Six Years Saving Comparison Financial Analytics";
    }


    protected function getData(): array
    {
        $currentYear = date('Y');
        $startYear = $currentYear - 6;
        $years = range($startYear, $currentYear);

        $datasets = [];

        $colors = [
            '#16A34A', '#1D4ED8', '#D97706', '#DC2626', '#7C3AED'
        ];


        foreach ($years as $index => $year) {

            $monthlySavings = Saving::query()->where('annual', '=', $year)
                ->where('status', '=', 'active')
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
                'fill' => 'start',
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
        return 'bar';
    }
    public function getColumnSpan(): int | string | array
    {
        return 2;
    }
}
