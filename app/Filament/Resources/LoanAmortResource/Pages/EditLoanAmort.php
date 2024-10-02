<?php

namespace App\Filament\Resources\LoanAmortResource\Pages;

use App\Filament\Resources\LoanAmortResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanAmort extends EditRecord
{
    protected static string $resource = LoanAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
