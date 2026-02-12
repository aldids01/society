<?php

namespace App\Filament\Resources\Guarantors\Widgets;

use App\Filament\Resources\Grains\GrainResource;
use App\Filament\Resources\Loans\LoanResource;
use App\Models\Guarantor;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GuarantorRequest extends TableWidget
{
    use HasPageShield;
    protected static ?int $sort = 4;
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Guarantor::query()->where('status', '=', 'pending')->where('member_id', '=', Auth::user()->member->slug)->whereHas('loan.loanAmorts', function ($query) {
                $query->where('status', '!=', 'paid')
                    ->where('status', '!=', 'complete');
            }))
            ->columns([
                TextColumn::make('loan.member.name')
                    ->label('Loan by'),
                TextColumn::make('loanAmorts_pending_sum')
                    ->label('Pending Loan')
                    ->numeric()
                    ->prefix('NGN')
                    ->getStateUsing(function ($record) {
                        return $record->loan->loanAmorts()
                            ->where('status', '!=', 'paid')
                            ->where('status', '!=', 'complete')
                            ->sum('principal');
                    }),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('authorize')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);

                        $user = $record->loan->member->user;
                        Notification::make()
                            ->title('Guarantor Request Approved')
                            ->body("{$record->member->name} Approved your guarantor request  Successfully")
                            ->actions([
                                Action::make('view')
                                    ->button()
                                    ->markAsRead()
                                    ->url("loans")
                            ])
                            ->sendToDatabase($user);

                        Notification::make()
                            ->title('Guarantee Approved')
                            ->success()
                            ->body('Guarantee Approved Successfully')
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
    public function getColumnSpan(): int | string | array
    {
        return 2;
    }
}
