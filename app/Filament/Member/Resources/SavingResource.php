<?php

namespace App\Filament\Member\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Saving;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Member\Resources\SavingResource\Pages;
use App\Filament\Member\Resources\SavingResource\RelationManagers;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Saving Report';
    protected static ?string $navigationGroup = 'Report';
    public static function getModelLabel(): string
    {
        return Auth::user()->name . ' Saving Report';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('applicant_id')
                    ->relationship('applicant', 'name')
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
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('annual'),
                Tables\Columns\TextColumn::make('January')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('February')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('March')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('April')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('May')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('June')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('July')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('August')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('September')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('October')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('November')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('December')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->summarize(Sum::make())
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('annual')
                    ->options(function () {
                        $years = range(now()->year, now()->year - 4);
                        return array_combine($years, $years);
                    }),
                SelectFilter::make('applicant_id')
                    //->relationship('applicant', 'name')
                    ->options([Auth::user()->applicant->staff_id => Auth::user()->applicant->name])
                    ->label('Applicant')
                    ->selectablePlaceholder(false)
                    ->default(Auth::user()->applicant->staff_id),
            ])->hiddenFilterIndicators()
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSavings::route('/'),
        ];
    }
}
