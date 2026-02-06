<?php

namespace Aldids\FilamentDbSync\Resources;


use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Aldids\FilamentDbSync\Models\DbSync;
use Aldids\FilamentDbSync\Resources\SyncResource\Pages\IndexDatabaseSync;
use BackedEnum;
use UnitEnum;

class SyncResource extends Resource
{
    protected static ?string $model = DbSync::class;

    protected static string | UnitEnum | null $navigationGroup = 'Settings';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?string $navigationLabel = 'Database Sync';

    protected static ?string $modelLabel = 'Database Sync';

    public static function Schema(Schema $schema):Schema
    {
        return $schema
            ->schema([
                Section::make('Database Sync')
                    ->schema([
                        TextInput::make('model')
                            ->label('Model'),

                        TextInput::make('action')
                            ->label('Action'),

                        TextInput::make('status')
                            ->label('Status'),

                        DatePicker::make('completed_at')
                            ->label('Completed At'),

                        DatePicker::make('failed_at')
                            ->label('Failed At'),

                        Textarea::make('failed_reason')
                            ->rows(4)
                            ->label('Failed Reason'),

                        Textarea::make('data')
                            ->rows(4)
                            ->label('Data'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('model')
                    ->label('Model'),

                TextColumn::make('data_total')
                    ->label('Data Total')
                    ->default(fn ($record) => count(json_decode($record->data, true))),

                TextColumn::make('action')
                    ->badge()
                    ->color(fn ($record) => match ($record->action) {
                        'push' => 'success',
                        'pull' => 'info',
                        default => 'neutral',
                    })
                    ->label('Action'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record) => match ($record->status) {
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'neutral',
                    })
                    ->label('Status'),

                TextColumn::make('created_at')
                    ->label('Timestamp'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
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
            'index' => IndexDatabaseSync::route('/'),
        ];
    }
}
