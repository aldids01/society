<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SavingResource\Pages;
use App\Filament\Resources\SavingResource\RelationManagers;
use App\Models\Saving;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SavingResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Saving::class;

    protected static ?string $navigationIcon = 'heroicon-m-calculator';
    protected static ?string $navigationGroup = 'Finance';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('applicant_id')
                    ->relationship('applicant', 'id')
                    ->required(),
                Forms\Components\TextInput::make('annual')
                    ->required(),
                Forms\Components\TextInput::make('January')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('February')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('March')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('April')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('May')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('June')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('July')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('August')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('September')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('October')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('November')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('December')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('total')
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant.staff_id')
                    ->label('Staff ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('applicant.name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('annual'),
                Tables\Columns\TextColumn::make('January')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('February')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('March')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('April')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('May')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('June')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('July')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('August')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('September')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('October')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('November')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('December')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'warning' => 'inactive',
                        'primary' => 'active',
                        'danger' => 'withdrawn',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'inactive',
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-exclamation-circle' => 'withdrawn',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
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
                    'active' => 'Active',
                    'inactive'=> 'Inactive',
                    'withdrawn' => 'Withdrawn'
                ])->default('active'),
                SelectFilter::make('annual')
                    ->options(function () {
                        $years = range(now()->year, now()->year - 10); // Adjust the range as needed
                        return array_combine($years, $years);
                    })
                    ->default(now()->year),
            ])
            ->defaultSort('applicant_id')
            ->defaultSort('applicant.name')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSavings::route('/'),
            'create' => Pages\CreateSaving::route('/create'),
            'view' => Pages\ViewSaving::route('/{record}'),
            'edit' => Pages\EditSaving::route('/{record}/edit'),
        ];
    }
}
