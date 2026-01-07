<?php

namespace App\Livewire;

use App\Models\Loan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
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
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

class LoanWithdrawal extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
            ->query(fn (): Builder => Loan::query()->whereBelongsTo(auth()->user()->member)->where('rate', '<', 6))
            ->columns([
                TextColumn::make('slug')
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
                    ->badge(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
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
        ];
    }


    #[Title('Saving Withdrawal')]
    public function render(): View
    {
        return view('livewire.loan-withdrawal');
    }
}
