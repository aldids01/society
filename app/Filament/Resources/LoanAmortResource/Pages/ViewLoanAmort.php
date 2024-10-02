<?php

namespace App\Filament\Resources\LoanAmortResource\Pages;

use App\Filament\Resources\LoanAmortResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoanAmort extends ViewRecord
{
    protected static string $resource = LoanAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
