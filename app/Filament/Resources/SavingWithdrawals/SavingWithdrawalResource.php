<?php

namespace App\Filament\Resources\SavingWithdrawals;

use App\Filament\Resources\Loans\LoanResource;
use App\Filament\Resources\SavingWithdrawals\Pages\ManageSavingWithdrawals;
use App\Models\Loan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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

class SavingWithdrawalResource extends Resource
{

    protected static ?string $model = Loan::class;

    protected static ?int $navigationSort = 2;
    protected static string | UnitEnum | null $navigationGroup = 'Savings';
    protected static ?string $modelLabel = 'Saving Withdrawal';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('slug')
                    ->default(fn() => Str::slug(Str::random(8))),
                Hidden::make('member_id')
                    ->default(fn() =>  auth()->user()->member->slug),
                TextInput::make('saved')
                    ->default(fn() => auth()->user()->member->savings->sum('total') ?? 0)
                    ->required()
                    ->readOnly()
                    ->prefix('NGN')
                    ->numeric(),
                Hidden::make('terms')
                    ->required()
                    ->default(1),
                Hidden::make('guarantor_type')
                    ->reactive()
                    ->default('self guaranteed'),
                FusedGroup::make([
                    TextInput::make('rate')
                        ->prefix('Rate (%):')
                        ->required()
                        ->default(3)
                        ->numeric()
                        ->readOnly(),
                    DatePicker::make('start_date')
                        ->prefix('Start Date:')
                        ->required()
                        ->columnSpan(2)
                        ->native(false)
                        ->default(now())
                        ->closeOnDateSelection()
                        ->displayFormat('F, Y'),

                ])->columns(3),

                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('NGN:'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
            ->columns([
                TextColumn::make('member.name')
                    ->searchable(),
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
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
                TextColumn::make('start_date')
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->schema(fn($schema) => LoanResource::infolist($schema))
                    ->slideOver(),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->status == 'pending'),
                ForceDeleteAction::make(),
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
            'index' => ManageSavingWithdrawals::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('rate', '<', 6);

        if (auth()->user()->hasRole('Member')) {
            $memberSlug = auth()->user()->member->slug;

            return $query->where('member_id', $memberSlug);
        }

        return $query;
    }
}
