<?php

namespace App\Livewire;

use App\Models\Grain;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
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

class GrainIndex extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
            ->query(fn (): Builder => Grain::query())
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
                    ->date('F, Y')
                    ->sortable(),
            ]) ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make('create')
                    ->schema(self::getSchemas())
                    ->modalWidth(Width::Large)
                    ->slideOver()
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema(self::infolist())
                    ->slideOver(),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->status == 'pending'),
                ForceDeleteAction::make(),
                RestoreAction::make(),
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
        ];
    }
    public static function getSchemas(): array
    {
        return [
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
                ->displayFormat('jS M, Y'),
        ];
    }
    #[title('Grain Request')]
    public function render(): View
    {
        return view('livewire.grain-index');
    }
}
