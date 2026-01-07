<?php

namespace App\Livewire;

use App\Models\Loan;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\FusedGroup;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class LoanApproval extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->paginated([5, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(5)
            ->deferLoading(true)
            ->query(fn (): Builder => Loan::query()->where('status', '!=','disbursed'))
            ->columns([
                TextColumn::make('member.name'),
                TextColumn::make('guarantor_type')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->badge(),
                TextColumn::make('saved')
                    ->numeric()
                    ->sortable(),
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
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
                                Placeholder::make('summary')
                                    ->label('Review Selected Loans')
                                    ->content(view('approval', [
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
}
