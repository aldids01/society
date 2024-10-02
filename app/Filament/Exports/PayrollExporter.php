<?php

namespace App\Filament\Exports;

use App\Models\Payroll;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PayrollExporter extends Exporter
{
    protected static ?string $model = Payroll::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employee.id')
                ->label('Staff ID'),
            ExportColumn::make('employee.f_name')
                ->formatStateUsing(fn (string $state): string => strtoupper($state))
                ->label('First Name'),
            ExportColumn::make('employee.l_name')
                ->formatStateUsing(fn (string $state): string => strtoupper($state))
                ->label('Last Name'),
            ExportColumn::make('employee.designation.name')
                ->label('Designation'),
            ExportColumn::make('employee.department.name')
                ->label('Department'),
            ExportColumn::make('gross'),
            ExportColumn::make('house'),
            ExportColumn::make('trans')
                ->label('Transport'),
            ExportColumn::make('meal'),
            ExportColumn::make('leave'),
            ExportColumn::make('basic'),
            ExportColumn::make('mercy')
                ->label('Mercy Fund'),
            ExportColumn::make('tax'),
            ExportColumn::make('pension'),
            ExportColumn::make('coop'),
            ExportColumn::make('loan'),
            ExportColumn::make('gas'),
            ExportColumn::make('llsc'),
            ExportColumn::make('hoh'),
            ExportColumn::make('other'),
            ExportColumn::make('net'),
            ExportColumn::make('gechaan'),
            ExportColumn::make('hohs')
                ->label('House of Hope'),
            ExportColumn::make('life')
                ->label('Lifeline'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your payroll export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
