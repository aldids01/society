<?php

namespace App\Filament\Member\Resources\LoanResource\Pages;

use App\Filament\Member\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
            ->visible(fn($record)=>$record->status === 'pending'),
        ];
    }
}
