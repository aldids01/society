<?php

namespace App\Livewire;

use App\Models\LoanAmort;
use App\Models\Member;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

class PendingLoans extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        $loan = LoanAmort::query()
            ->where('member_id', '=', auth()->user()->member->slug)
            ->whereNotIn('status', ['paid', 'complete'])
            ->pluck('loan_id', 'loan_id')
            ->firstOrFail();
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->query(fn (): Builder => LoanAmort::query()->where('member_id', '=', auth()->user()->member->slug))
            ->columns([
                TextColumn::make('member.name')
                    ->searchable(),
                TextColumn::make('annual'),
                TextColumn::make('period')
                    ->searchable(),
                TextColumn::make('interest')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('principal')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('payment')
                    ->numeric()
                    ->summarize(Sum::make()->label(''))
                    ->sortable(),
                TextColumn::make('start_balance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('end_balance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),

            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ])
                    ->multiple()
                    ->default(['pending', 'overdue'] ),
                SelectFilter::make('period')
                    ->options(function () {
                        return collect(range(1, 12))->mapWithKeys(function ($month) {
                            $monthName = Carbon::create()->month($month)->format('F');
                            return [$monthName => $monthName];
                        })->toArray();
                    }),
                SelectFilter::make('annual')
                    ->options(function () {
                        $currentYear = now()->year;
                        $years = range($currentYear - 5, $currentYear + 5);
                        return array_combine($years, $years);
                    }),
                SelectFilter::make('loan_id')
                    ->label('Current Loan')
                    ->selectablePlaceholder(false)
                    ->options(function () use ($loan) {
                        return [$loan => 'Active'];
                    })->default($loan),
            ], layout: FiltersLayout::AboveContent)
            ->deselectAllRecordsWhenFiltered(false)
            ->hiddenFilterIndicators()
            ->filtersResetActionPosition(FiltersResetActionPosition::Footer)
            ->persistFiltersInSession()
            ->deferFilters(true)
            ->deferLoading(true)
            ->persistSortInSession()
            ->headerActions([
                //
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

    #[title('Pending Loans')]
    public function render(): View
    {
        return view('livewire.pending-loans');
    }
}
