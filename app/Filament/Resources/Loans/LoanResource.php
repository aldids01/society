<?php

namespace App\Filament\Resources\Loans;

use App\Filament\Resources\Loans\Pages\ManageLoans;
use App\Models\Guarantor;
use App\Models\Loan;
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
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use UnitEnum;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?int $navigationSort = 5;
    protected static string | UnitEnum | null $navigationGroup = 'Loans';
    protected static ?string $modelLabel = 'Request';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

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

                Hidden::make('guarantor_type')
                    ->reactive()
                    ->default('self guaranteed'),
                FusedGroup::make([
                    TextInput::make('rate')
                        ->prefix('Rate (%):')
                        ->required()
                        ->numeric()
                        ->readOnly(),
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->prefix('Amount (NGN):')
                        ->columnSpan(2)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $rate = self::calculateRate($state, $get('saved'));
                            $set('rate', $rate);
                            if ($get('rate') > 6) {
                                $set('guarantor_type', 'applicant');
                            } else {
                                $set('guarantor_type', 'self guaranteed');
                            }
                        }),
                ])->columns(3),

                FusedGroup::make([
                    Select::make('terms')
                        ->prefix('Terms:')
                        ->required()
                        ->options(fn() => collect(range(1, 12))->mapWithKeys(fn($month) => [$month => "$month Month" . ($month > 1 ? 's' : '')]))
                        ->default(1),
                    DatePicker::make('start_date')
                        ->prefix('Start Date:')
                        ->required()
                        ->native(false)
                        ->closeOnDateSelection()
                        ->displayFormat('F, Y'),
                ])->columns(2),
                Repeater::make('Guarantorship')
                    ->columnSpanFull()
                    ->reactive()
                    ->compact()
                    ->collapsible()
                    ->relationship('guarantors')
                    ->schema([
                        FusedGroup::make([
                            Select::make('member_id')
                                ->relationship('member', 'name', modifyQueryUsing: function ($query) {
                                    $query->where('status', 'active');
                                })
                                ->searchable()
                                ->prefix('Fullname:')
                                ->preload()
                                ->native(false)
                                ->inlineLabel()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->required(),
                            TextInput::make('amount')
                                ->required()
                                ->prefix('Amount (NGN):')
                                ->inlineLabel()
                                ->numeric(),
                        ]),
                    ])->addActionLabel('Add another guarantor')->addActionAlignment(Alignment::Start)->hidden(fn(callable $get) => $get('guarantor_type') != 'applicant'),
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
                RepeatableEntry::make('loanAmorts')
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
                    ]),
                RepeatableEntry::make('guarantors')
                    ->placeholder('This loan does not require a guarantor.')
                    ->table([
                        TableColumn::make('Name')
                            ->width('40%'),
                        TableColumn::make('Amount')
                            ->width('30%')
                            ->alignEnd(),
                        TableColumn::make('Status')
                            ->width('30%')
                            ->alignEnd(),
                    ])
                    ->schema([
                        TextEntry::make('member.name') ->alignStart(),
                        TextEntry::make('amount') ->numeric()->alignEnd(),
                        TextEntry::make('status')
                            ->formatStateUsing(fn ($state) => ucfirst($state))
                            ->badge()
                            ->alignEnd(),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
            ->columns([
                TextColumn::make('member.name')
                    ->searchable(),
                TextColumn::make('guarantor_type')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
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
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
                TextColumn::make('start_date')
                    ->label('Start month')
                    ->date('F Y')
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
            'index' => ManageLoans::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('rate', '>=', 6);

        if (auth()->user()->hasRole('Member')) {
            $memberSlug = auth()->user()->member->slug;

            return $query->where('member_id', $memberSlug);
        }

        return $query;
    }

    protected static function calculateRate($amount, $savings): int
    {
        if ($amount <= 4 * $savings and $amount > 3 * $savings) {
            return 24;
        } elseif ($amount <= 3 * $savings and $amount > 2 * $savings) {
            return 18;
        } elseif ($amount <= 2 * $savings and $amount > 1 * $savings) {
            return 12;
        } elseif ($amount <= 1 * $savings) {
            return 6;
        } elseif ($savings < 1) {
            return 18;
        } else {

            Notification::make()
                ->title('Excessive Loan Amount')
                ->body('The loan amount you enter is beyond the constitution of this society, please kindly reduce your loan amount to equals your saving or four (4) times of your saving. Thanks.')
                ->danger()
                ->color('danger')
                ->persistent()
                ->send();
            return 0;
        }
    }
}
