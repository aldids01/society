<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->requiredMapping()
                ->rules(['required', 'max:150']),
            ImportColumn::make('f_name')
                ->requiredMapping()
                ->rules(['required', 'max:150']),
            ImportColumn::make('l_name')
                ->requiredMapping()
                ->rules(['required', 'max:150']),
            ImportColumn::make('gender')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('phone')
                ->requiredMapping()
                ->rules(['required', 'max:13']),
            ImportColumn::make('dob')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('email')
                ->rules(['email', 'max:255']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('account')
                ->requiredMapping()
                ->rules(['required', 'max:10']),
            ImportColumn::make('rsa_status')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('rsa_account')
                ->rules(['max:255']),
            ImportColumn::make('gross')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('user')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('department')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('designation')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('staff')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('kin_name')
                ->rules(['max:150']),
            ImportColumn::make('kin_phone')
                ->rules(['max:150']),
            ImportColumn::make('kin_address')
                ->rules(['max:255']),
            ImportColumn::make('relation')
                ->rules(['max:255']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        // return Employee::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Employee();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your employee import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
