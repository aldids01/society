<?php

namespace App\Livewire;

use App\Filament\Imports\SavingImporter;
use App\Models\Member;
use App\Models\Saving;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ImportAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class SavingsReport extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
            ->query(fn (): Builder => Saving::query())
            ->groups([
                Group::make('annual')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
                Group::make('member.name')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible()
            ])
            ->collapsedGroupsByDefault()
            ->columns([
                TextColumn::make('member.name')
                    ->formatStateUsing(fn ($record) => strtoupper($record->member->name)),
                TextColumn::make('annual'),
                TextColumn::make('January')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('February')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('March')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('April')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('May')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('June')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('July')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('August')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('September')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('October')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('November')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('December')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('total')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'withdrawn' => 'Withdrawn'
                    ]),
                SelectFilter::make('annual')
                    ->options(function () {
                        $years = range(now()->year - 5, now()->year + 5); // Adjust the range as needed
                        return array_combine($years, $years);
                    }),
                SelectFilter::make('member_id')
                    ->label('Member')
                    ->searchable()
                    ->options(fn (): array => Member::query()->where('status', '=', 'active')->pluck('name', 'slug')->all())
            ] , layout: FiltersLayout::AboveContent)
            ->defaultGroup('member.name')
            ->groupsOnly()
            ->deselectAllRecordsWhenFiltered(false)
            ->hiddenFilterIndicators()
            ->filtersResetActionPosition(FiltersResetActionPosition::Footer)
            ->persistFiltersInSession()
            ->deferFilters(true)
            ->deferLoading(true)
            ->persistSortInSession()
            ->headerActions([
                ImportAction::make()
                    ->importer(SavingImporter::class)
                    ->modalWidth(Width::ExtraSmall)
                    ->modalCancelAction(false)
                    ->color('primary')
                    ->modalFooterActionsAlignment(Alignment::Center)
                    ->slideOver()
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    #[Layout('components.layouts.admin')]
    #[title('Savings Report')]
    public function render(): View
    {
        return view('livewire.savings-report');
    }
}
