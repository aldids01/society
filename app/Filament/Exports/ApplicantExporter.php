<?php

namespace App\Filament\Exports;

use App\Models\Applicant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ApplicantExporter extends Exporter
{
    protected static ?string $model = Applicant::class;
    public static function getQuery(): Builder
    {
        return Applicant::query()
            ->with(['loanAmort' => function ($query) {
                $query->where('period', date('F'))
                    ->where('annual', date('Y'))
                    ->where('status', '=', 'pending');;
            }, 'grainAmort' => function ($query) {
                $query->where('period', date('F'))
                    ->where('annual', date('Y'))
                    ->where('status', '=', 'pending');;
            }]);
    }
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('staff_id'),
            ExportColumn::make('name')
                ->formatStateUsing(fn ($state) => strtoupper($state)),
            ExportColumn::make('saving'),
            ExportColumn::make('loanAmort.interest')
                ->label('Loan Interest')
                ->formatStateUsing(fn ($state) => $state ?? 0),
            ExportColumn::make('loanAmort.principal')
                ->label('Loan Principal')
                ->formatStateUsing(fn ($state) => $state ?? 0),
            ExportColumn::make('grainAmort.interest')
                ->label('Grain Interest')
                ->formatStateUsing(fn ($state) => $state ?? 0),
            ExportColumn::make('grainAmort.principal')
                ->label('Grain Principal')
                ->formatStateUsing(fn ($state) => $state ?? 0),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your applicant export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
