<?php

namespace App\Filament\Member\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\GrainAmort;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Member\Resources\GrainAmortResource\Pages;
use App\Filament\Member\Resources\GrainAmortResource\RelationManagers;

class GrainAmortResource extends Resource
{
    protected static ?string $model = GrainAmort::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Pending Grain';
    protected static ?string $navigationGroup = 'Report';
    public static function getModelLabel(): string
    {
        return Auth::user()->name . ' Pending Grain';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('annual'),
                Tables\Columns\TextColumn::make('period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interest')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('principal')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'primary' => 'paid',
                    'danger' => 'overdue',
                ])
                ->icons([
                    'heroicon-o-x-circle' => 'pending',
                    'heroicon-o-check-circle' => 'paid',
                    'heroicon-o-exclamation-circle' => 'overdue',
                ])
                ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'paid'=> 'Paid',
                    'overdue' => 'Overdue'
                ])->default('pending'),
                SelectFilter::make('grain_owner')
                    //->relationship('applicant', 'name')
                    ->options([Auth::user()->applicant->staff_id => Auth::user()->applicant->name])
                    ->label('Applicant')
                    ->selectablePlaceholder(false)
                    ->default(Auth::user()->applicant->staff_id),
            ])->hiddenFilterIndicators()
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGrainAmorts::route('/'),
        ];
    }
}
