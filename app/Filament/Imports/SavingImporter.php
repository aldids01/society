<?php

namespace App\Filament\Imports;

use App\Models\Saving;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SavingImporter extends Importer
{
    protected static ?string $model = Saving::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('member')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('annual')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('January')

                ->numeric(),
            ImportColumn::make('February')

                ->numeric(),
            ImportColumn::make('March')

                ->numeric(),
            ImportColumn::make('April')

                ->numeric(),
            ImportColumn::make('May')

                ->numeric(),
            ImportColumn::make('June')

                ->numeric(),
            ImportColumn::make('July')

                ->numeric(),
            ImportColumn::make('August')

                ->numeric(),
            ImportColumn::make('September')

                ->numeric(),
            ImportColumn::make('October')

                ->numeric(),
            ImportColumn::make('November')

                ->numeric(),
            ImportColumn::make('December')

                ->numeric(),
        ];
    }

    public function resolveRecord(): Saving
    {
        return Saving::firstOrNew([
            'member_id' => $this->data['member'],
            'annual' => $this->data['annual'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your saving import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
