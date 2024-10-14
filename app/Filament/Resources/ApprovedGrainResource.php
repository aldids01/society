<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovedGrainResource\Pages;
use App\Filament\Resources\ApprovedGrainResource\RelationManagers;
use App\Models\ApprovedGrain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ApprovedGrainResource extends Resource
{
    protected static ?string $model = ApprovedGrain::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Approvals';
    protected static ?string $modalLabel = 'Grain Approvals';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grain_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('checkedby')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('checkeddate'),
                Forms\Components\TextInput::make('approvedby')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('approveddate'),
                Forms\Components\TextInput::make('disbursedby')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('disburseddate'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grain.applicant.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grain.amount')
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
                        ->url(fn ($record) => url("/member/grains/{$record->grain->id}"))
                        ->label('View Grain'),
                    Tables\Actions\EditAction::make()
                        ->visible(fn($record)=>$record->grain->status === 'pending'),
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
                        ->visible(fn($record)=>$record->grain->status === 'approved'),
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
                        ->visible(fn($record)=>$record->grain->status === 'checked'),
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
                        ->visible(fn($record)=>$record->grain->status === 'checked'),
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
                        ->visible(fn($record)=>$record->grain->status === 'pending'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageApprovedGrains::route('/'),
        ];
    }
}
