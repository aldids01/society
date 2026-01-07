<?php

namespace App\Filament\Exports;

use App\Models\Member;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class MemberExporter extends Exporter
{
    protected static ?string $model = Member::class;

    public static function getQuery(): Builder
    {
        return Member::query()->where('status', 'active')
            ->with(['loanAmorts' => function ($query) {
                $query->where('period', date('F'))
                    ->where('annual', date('Y'))
                    ->where('status', '=', 'pending');
            }, 'grainAmorts' => function ($query) {
                $query->where('period', date('F'))
                    ->where('annual', date('Y'))
                    ->where('status', '=', 'pending');
            }])->orderBy('name', 'asc');
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('slug')
                ->label('Staff ID'),
            ExportColumn::make('name')
                ->formatStateUsing(fn($state) => strtoupper($state)),
            ExportColumn::make('saving'),
            ExportColumn::make('loanAmorts.interest')
                ->label('Loan Interest')
                ->formatStateUsing(fn($state) => $state ?? 0),
            ExportColumn::make('loanAmorts.principal')
                ->label('Loan Principal')
                ->formatStateUsing(fn($state) => $state ?? 0),
            ExportColumn::make('grainAmorts.interest')
                ->label('Grain Interest')
                ->formatStateUsing(fn($state) => $state ?? 0),
            ExportColumn::make('grainAmorts.principal')
                ->label('Grain Principal')
                ->formatStateUsing(fn($state) => $state ?? 0),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your member export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
