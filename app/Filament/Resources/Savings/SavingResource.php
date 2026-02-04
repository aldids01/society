<?php

namespace App\Filament\Resources\Savings;

use App\Filament\Resources\Savings\Pages\ManageSavings;
use App\Models\Saving;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use UnitEnum;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static ?int $navigationSort = 3;
    protected static string | UnitEnum | null $navigationGroup = 'Savings';

    protected static ?string $modelLabel = 'Saving History';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static ?string $recordTitleAttribute = 'member.name';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('member.name')
                    ->label('Member'),
                TextEntry::make('annual'),
                TextEntry::make('January')
                    ->numeric(),
                TextEntry::make('February')
                    ->numeric(),
                TextEntry::make('March')
                    ->numeric(),
                TextEntry::make('April')
                    ->numeric(),
                TextEntry::make('May')
                    ->numeric(),
                TextEntry::make('June')
                    ->numeric(),
                TextEntry::make('July')
                    ->numeric(),
                TextEntry::make('August')
                    ->numeric(),
                TextEntry::make('September')
                    ->numeric(),
                TextEntry::make('October')
                    ->numeric(),
                TextEntry::make('November')
                    ->numeric(),
                TextEntry::make('December')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Saving $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        $total = Saving::query()->where('member_id', '=', auth()->user()->member->slug)->sum('total');
        return $table
            ->recordTitleAttribute('member.name')
            ->heading(fn()=> 'Total Saving: ' .Number::format($total, 2).'  ('.ucwords(Number::spell($total)).')')
            ->defaultPaginationPageOption(25)
            ->deferLoading(true)
            ->query(fn (): Builder => Saving::query()->where('member_id', '=', auth()->user()->member->slug))
            ->paginated([10, 25, 50, 100, 'all'])
            ->columns([
                TextColumn::make('annual'),
                TextColumn::make('January')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('February')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('March')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('April')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('May')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('June')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('July')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('August')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('September')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('October')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('November')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('December')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
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
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalWidth(Width::Small)
                    ->slideOver(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSavings::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
