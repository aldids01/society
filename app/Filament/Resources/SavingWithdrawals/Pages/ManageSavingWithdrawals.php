<?php

namespace App\Filament\Resources\SavingWithdrawals\Pages;

use App\Filament\Resources\SavingWithdrawals\SavingWithdrawalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageSavingWithdrawals extends ManageRecords
{
    protected static string $resource = SavingWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::Large)
                ->slideOver(),
        ];
    }


}
