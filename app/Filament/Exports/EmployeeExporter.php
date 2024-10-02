<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('f_name'),
            ExportColumn::make('l_name'),
            ExportColumn::make('gender'),
            ExportColumn::make('phone'),
            ExportColumn::make('dob'),
            ExportColumn::make('email'),
            ExportColumn::make('address'),
            ExportColumn::make('account'),
            ExportColumn::make('rsa_status'),
            ExportColumn::make('rsa_account'),
            ExportColumn::make('gross'),
            ExportColumn::make('user.name'),
            ExportColumn::make('department.name'),
            ExportColumn::make('designation.name'),
            ExportColumn::make('staff.name'),
            ExportColumn::make('kin_name'),
            ExportColumn::make('kin_phone'),
            ExportColumn::make('kin_address'),
            ExportColumn::make('relation'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
