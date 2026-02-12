<?php

namespace App\Filament\Resources\Grains;

use App\Filament\Resources\Grains\Pages\ManageGrains;
use App\Models\Grain;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class GrainResource extends Resource
{
    protected static ?string $model = Grain::class;
    protected static ?int $navigationSort = 007;
    protected static string | UnitEnum | null $navigationGroup = 'Grains';

    protected static ?string $modelLabel = 'Request';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('slug')
                    ->default(fn() => Str::slug(Str::random(8))),
                Hidden::make('member_id')
                    ->default(fn() =>  auth()->user()->member->slug),
                TextInput::make('rate')
                    ->prefix('Rate (%):')
                    ->required()
                    ->default(10)
                    ->numeric()
                    ->readOnly(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('(NGN):'),
                Select::make('terms')
                    ->prefix('Terms:')
                    ->required()
                    ->options(fn() => collect(range(1, 3))->mapWithKeys(fn($month) => [$month => "$month Month" . ($month > 1 ? 's' : '')]))
                    ->default(1),
                DatePicker::make('start_date')
                    ->prefix('Start Date:')
                    ->required()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->displayFormat('F, Y'),
            ])->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('saved')
                    ->hiddenLabel()
                    ->prefix('Amount Saved NGN ')
                    ->alignEnd()
                    ->inlineLabel()
                    ->numeric(),
                FusedGroup::make([
                    TextEntry::make('rate')
                        ->inlineLabel()
                        ->prefix('%'),
                    TextEntry::make('amount')
                        ->numeric()
                        ->inlineLabel()
                        ->prefix('NGN'),
                ])->columns(2),
                FusedGroup::make([
                    TextEntry::make('terms')->inlineLabel()->suffix(' Month(s)'),
                    TextEntry::make('start_date')
                        ->inlineLabel()
                        ->date('jS F, Y'),
                ])->columns(2),
                RepeatableEntry::make('grainAmorts')
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make('Annual'),
                        TableColumn::make('Period'),
                        TableColumn::make('Interest')->alignEnd(),
                        TableColumn::make('Principal')->alignEnd(),
                        TableColumn::make('Payment')->alignEnd(),
                        TableColumn::make('Start')->alignEnd(),
                        TableColumn::make('End')->alignEnd(),
                        TableColumn::make('Status')->alignCenter(),
                    ])
                    ->schema([
                        TextEntry::make('annual') ->alignStart(),
                        TextEntry::make('period') ->alignStart(),
                        TextEntry::make('interest') ->numeric()->alignEnd(),
                        TextEntry::make('principal') ->numeric()->alignEnd(),
                        TextEntry::make('payment') ->numeric()->alignEnd(),
                        TextEntry::make('start_balance') ->numeric()->alignEnd(),
                        TextEntry::make('end_balance') ->numeric()->alignEnd(),
                        TextEntry::make('status')->badge()->alignCenter()->formatStateUsing(fn ($state) => ucfirst($state)),
                    ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitle('member.name')
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
            ->columns([
                TextColumn::make('member.name')
                    ->searchable(),
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
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
                TextColumn::make('start_date')
                    ->label('Start month')
                    ->date('F, Y')
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->recordTitle(fn ($record): string => $record->member?->name ?? 'Member Loan Request')
                    ->slideOver(),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->status == 'pending'),
                ForceDeleteAction::make(),
                RestoreAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => $record->status == 'pending')
                    ->modalWidth(Width::Large)
                    ->slideOver()
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
            'index' => ManageGrains::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (auth()->user()->hasRole('Member')) {
            $memberSlug = auth()->user()->member->slug;

            return $query->where('member_id', $memberSlug);
        }

        return $query;
    }
}
