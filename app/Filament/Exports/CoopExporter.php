<?php

namespace App\Filament\Exports;

use App\Models\Payroll;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CoopExporter extends Exporter
{
    protected static ?string $model = Payroll::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee_id')
                ->label('Staff Id'),
            ExportColumn::make('employee.full_name')
                ->label('Employee')
                ->getStateUsing(function ($record) {
                    return $record->employee->f_name . ' ' . $record->employee->l_name;
                }),
            ExportColumn::make('saved')
                ->label('Saving'),
            ExportColumn::make('lint')
                ->label('Loan Interest'),
            ExportColumn::make('lprin')
                ->label('Loan Principal'),
            ExportColumn::make('gint')
                ->label('Grain Interest'),
            ExportColumn::make('gprin')
                ->label('Grain Principal'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your coop export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
