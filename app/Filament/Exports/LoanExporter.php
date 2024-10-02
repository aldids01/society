<?php

namespace App\Filament\Exports;

use App\Models\Payroll;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class LoanExporter extends Exporter
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
            ExportColumn::make('loan')
                ->label('Amount'),
            ExportColumn::make('loan_gechaan')
                ->label('GECHAAN')
                ->getStateUsing(function ($record) {
                    if ($record->employee->department->category->name === 'GECHAAN') {
                        return number_format($record->loan, 2);
                    } else {
                        return number_format(0, 2);
                    }
                }),
            ExportColumn::make('loan_llsc')
                ->label('LLSC')
                ->getStateUsing(function ($record) {
                    if ($record->employee->department->category->name === 'LLSC') {
                        return number_format($record->loan, 2);
                    } else {
                        return number_format(0, 2);
                    }
                }),
            ExportColumn::make('loan_hoh')
                ->label('HOH')
                ->getStateUsing(function ($record) {
                    if ($record->employee->department->category->name === 'HOH') {
                        return number_format($record->loan, 2);
                    } else {
                        return number_format(0, 2);
                    }
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your loan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
