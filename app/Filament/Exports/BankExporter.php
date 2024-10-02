<?php

namespace App\Filament\Exports;

use App\Models\Bank;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BankExporter extends Exporter
{
    protected static ?string $model = Bank::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('SN'),
            ExportColumn::make('employee_id')
                ->label('Staff ID'),
            ExportColumn::make('description')
                ->formatStateUsing(fn (string $state): string => strtoupper($state)),
            ExportColumn::make('account'),
            ExportColumn::make('payment')
                ->formatStateUsing(fn ($state) => number_format($state, 2)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your bank export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
