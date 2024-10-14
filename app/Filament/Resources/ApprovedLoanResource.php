<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ApprovedLoan;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource\RelationManagers;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class ApprovedLoanResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ApprovedLoan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Approvals';
    protected static ?string $modalLabel = 'Loan Approvals';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'checked',
            'disbursed',
            'approve',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('loan_id')
                    ->relationship('loan', 'id')
                    ->required(),
                Forms\Components\TextInput::make('checkedby')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DateTimePicker::make('checkeddate'),
                Forms\Components\TextInput::make('approvedby')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DateTimePicker::make('approveddate'),
                Forms\Components\TextInput::make('disbursedby')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DateTimePicker::make('disburseddate'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan.applicant.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan.amount')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('check.name')
                    ->placeholder('Pending')
                    ->searchable(),
                Tables\Columns\TextColumn::make('checkeddate')
                    ->dateTime()
                    ->placeholder('Pending')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approve.name')
                    ->placeholder('Pending')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approveddate')
                    ->placeholder('Pending')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disburse.name')
                    ->placeholder('Pending')
                    ->searchable(),
                Tables\Columns\TextColumn::make('disburseddate')
                    ->placeholder('Pending')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved'=> 'Approved',
                        'rejected' => 'Rejected',
                        'disbursed' => 'Disbursed'
                    ])->query(function ($query) {
                        return $query->where('status', '!=', 'disbursed');
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->url(fn ($record) => url("/member/loans/{$record->loan->id}"))
                        ->label('View Loan'),
                    Tables\Actions\EditAction::make()
                        ->visible(fn($record)=>$record->loan->status === 'pending'),
                    Tables\Actions\Action::make('disbursed')
                            ->label('Disbursed')
                            ->action(function ($record) {
                                $record->loan->update(['status' => 'disbursed']);
                                $record->update([
                                    'status' => 'disbursed',
                                    'disbursedby' => Auth::user()->applicant->staff_id,
                                    'disburseddate' => now(),
                                ]);
                            })
                            ->requiresConfirmation()
                            ->color('primary')
                            ->icon('heroicon-s-truck')
                            ->visible(fn($record)=>$record->loan->status === 'approved'),
                    Tables\Actions\Action::make('approve')
                            ->label('Approve')
                            ->action(function ($record) {
                                $record->loan->update(['status' => 'approved']);
                                $record->update([
                                    'status' => 'approved',
                                    'approvedby' => Auth::user()->applicant->staff_id,
                                    'approveddate' => now(),
                                ]);
                            })
                            ->requiresConfirmation()
                            ->color('success')
                            ->icon('heroicon-s-check')
                            ->visible(fn($record)=>$record->loan->status === 'checked'),
                    Tables\Actions\Action::make('reject')
                            ->label('Reject')
                            ->action(function ($record) {
                                $record->loan->update(['status' => 'rejected']);
                                $record->update([
                                    'status' => 'rejected',
                                    'approvedby' => Auth::user()->applicant->staff_id,
                                    'approveddate' => now(),
                                ]);
                            })
                            ->requiresConfirmation()
                            ->color('danger')
                            ->icon('heroicon-s-x-circle')
                            ->visible(fn($record)=>$record->loan->status === 'checked'),
                    Tables\Actions\Action::make('checked')
                            ->label('Checked')
                            ->action(function ($record) {
                                $record->loan->update(['status' => 'checked']);
                                $record->update([
                                    'status' => 'checked',
                                    'checkedby' => Auth::user()->applicant->staff_id,
                                    'checkeddate' => now(),
                                ]);
                            })
                            ->requiresConfirmation()
                            ->color('info')
                            ->icon('heroicon-s-pencil-square')
                            ->visible(fn($record)=>$record->loan->status === 'pending'),
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                 //
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageApprovedLoans::route('/'),
        ];
    }
}
