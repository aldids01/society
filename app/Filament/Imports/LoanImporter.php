<?php

namespace App\Filament\Imports;

use App\Models\Payroll;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Carbon;

class LoanImporter extends Importer
{
    protected static ?string $model = Payroll::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('employee_id')
                ->label('Staff Id')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('loan')
                ->label('Amount')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?Payroll
    {
        // Get the employee_id from the imported data
        $employeeId = $this->data['employee_id'] ?? null;

        // Check if employee_id is set and is not null
        if ($employeeId) {
            // Get the current month
            $currentMonth = Carbon::now()->format('m');

            // Check if a record with the given employee_id and current month exists
            $record = Payroll::where('employee_id', $employeeId)
                ->whereMonth('created_at', $currentMonth)
                ->first();

            // If a record exists, return it to update
            if ($record) {
                return $record;
            }
        }

        // If no record exists, create a new one
        return new Payroll();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your loan import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
