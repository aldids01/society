<?php

namespace App\Livewire;

use App\Filament\Exports\LoanExporter;
use App\Models\Loan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

class LoansTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;



    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->query(fn (): Builder => Loan::query()->where('member_id', '=', auth()->user()->member->slug)->where('rate', '>=', 6))
            ->defaultPaginationPageOption(25)
            ->deferLoading(true)
            ->columns([
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('guarantor_type')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
                TextColumn::make('saved')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rate')
                    ->numeric()
                    ->suffix(' %')
                    ->sortable(),
                TextColumn::make('terms')
                    ->numeric()
                    ->suffix(' Month(s)')
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

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make('create')
                ->schema(self::getSchemas())->modalWidth(Width::Large)
                    ->slideOver(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema(self::infolist())
                    ->slideOver(),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->status == 'pending'),
                ForceDeleteAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => $record->status == 'pending')
                    ->schema(self::getSchemas())
                    ->modalWidth(Width::Large)
                    ->slideOver()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    #[title('Loan Requests')]
    public function render(): View
    {
        return view('livewire.loans-table');
    }

    public static function infolist(): array
    {
        return [
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
                ])
            ];
    }
    public static function getSchemas(): array
    {
        return [
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
                    ->displayFormat('jS M, Y'),
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
        ];
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
