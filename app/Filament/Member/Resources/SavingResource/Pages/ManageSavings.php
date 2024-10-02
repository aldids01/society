<?php

namespace App\Filament\Member\Resources\SavingResource\Pages;

use Filament\Actions;
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Member\Resources\SavingResource;

class ManageSavings extends ManageRecords
{
    protected static string $resource = SavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Update Monthly Saving')
            ->requiresConfirmation()
            ->form([
                TextInput::make('amount')
                ->hint('Min of NGN 1,000 Current and future months.')
                ->numeric()
                ->minValue(1000)
            ])
            ->action(function($data){
                $applicant = Applicant::where('staff_id', Auth::user()->applicant->staff_id)->first();
                if($applicant){
                    $update = $applicant->update([
                        'saving' => $data['amount'],
                    ]);

                    if($update){
                        $recipient = Auth::user();
                        Notification::make()
                            ->title('Monthly Saving Updated')
                            ->body($recipient->name .' Changed his/her monthly saving.')
                            ->sendToDatabase($recipient);

                        Notification::make()
                            ->title('Monthly Saving Updated Successfully')
                            ->body('You changed your monthly saving for this month and future months to '.number_format($data['amount'], 2).' You can change this in the future.')
                            ->persistent()
                            ->success()
                            ->send();
                    }
                }
            })
            ->slideOver(),
        ];
    }
}
