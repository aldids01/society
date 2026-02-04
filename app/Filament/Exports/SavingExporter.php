<?php

namespace App\Filament\Exports;

use App\Models\Saving;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class SavingExporter extends Exporter
{
    protected static ?string $model = Saving::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('member.name'),
            ExportColumn::make('annual'),
            ExportColumn::make('January'),
            ExportColumn::make('February'),
            ExportColumn::make('March'),
            ExportColumn::make('April'),
            ExportColumn::make('May'),
            ExportColumn::make('June'),
            ExportColumn::make('July'),
            ExportColumn::make('August'),
            ExportColumn::make('September'),
            ExportColumn::make('October'),
            ExportColumn::make('November'),
            ExportColumn::make('December'),
            ExportColumn::make('total'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your saving export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
