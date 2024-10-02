<?php

namespace App\Filament\Imports;

use App\Models\Saving;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SavingImporter extends Importer
{
    protected static ?string $model = Saving::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('applicant')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make(date('F'))
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?Saving
    {
        return Saving::firstOrNew([
            'applicant_id' => $this->data['applicant'],
            'annual' => date('Y'),
        ]);

        return new Saving();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your saving import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
