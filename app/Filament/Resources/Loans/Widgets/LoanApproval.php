<?php

namespace App\Filament\Resources\Loans\Widgets;

use App\Filament\Resources\Loans\LoanResource;
use App\Models\Loan;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LoanApproval extends TableWidget
{
    use HasPageShield;
    protected static ?int $sort = 3;
    public function table(Table $table): Table
    {
        return $table
            ->paginated([5, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(5)
            ->deferLoading(true)
            ->query(fn (): Builder => Loan::query()->where('status', '!=','disbursed'))
            ->columns([
                TextColumn::make('member.name'),
                TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('terms')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'checked',
                        'primary' => 'disbursed',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-s-x-mark' => 'pending',
                        'heroicon-s-check' => 'checked',
                        'heroicon-s-x-circle' => 'rejected',
                        'heroicon-s-check-badge' => 'approved',
                        'heroicon-s-truck' => 'disbursed',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->badge(),
                TextColumn::make('start_date')
                    ->date('F Y')
                    ->sortable(),
//                TextColumn::make('created_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                TextColumn::make('updated_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                TextColumn::make('deleted_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //serInfolist::configure($schema);
            ])
            ->recordActions([
                ViewAction::make()
                    ->recordTitle(fn ($record): string => $record->member?->name ?? 'Member Loan Request')
                    ->schema(fn($schema) => LoanResource::infolist($schema))
                    ->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approval')
                        ->icon('heroicon-s-check')
                        ->modalHeading('Approval')
                        ->modalDescription('Are you sure you want to approve the selected loans?')
                        ->modalWidth(Width::Small)
                        ->modalFooterActionsAlignment(Alignment::Center)
                        ->modalCancelAction(false)
                        ->modalSubmitActionLabel('Approve')
                        ->schema(function (Collection $records) {
                            return [
                                TextEntry::make('summary')
                                    ->hiddenLabel()
                                    ->state(view('approval', [
                                        'records' => $records,
                                    ])),
                            ];
                        })
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $nextStatus = match ($record->status) {
                                    'pending'  => 'checked',
                                    'checked'  => 'approved',
                                    'approved' => 'disbursed',
                                    default    => null, // No change if status doesn't match
                                };

                                if ($nextStatus) {
                                    $record->update(['status' => $nextStatus]);
                                }

                                $user = $record->member->user;
                                Notification::make()
                                    ->title("Loan $nextStatus")
                                    ->body("Your loan request have been successfully $nextStatus")
                                    ->actions([
                                        Action::make('view')
                                            ->button()
                                            ->markAsRead()
                                            ->url("loans")
                                    ])
                                    ->sendToDatabase($user);
                            }


                            Notification::make()
                                ->title('Approved')
                                ->body('Selected Loans Approved successfully')
                                ->success()
                                ->send();
                        })
                        ->slideOver()
                ]),
            ]);
    }
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->hasRole('super_admin');
    }
    public function getColumnSpan(): int | string | array
    {
        return 1;
    }
}
