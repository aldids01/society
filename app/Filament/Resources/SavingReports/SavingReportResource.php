<?php

namespace App\Filament\Resources\SavingReports;

use App\Filament\Resources\SavingReports\Pages\ManageSavingReports;
use App\Filament\Resources\SavingReports\Widgets\QuarterlyReportStats;
use App\Models\Member;
use App\Models\Saving;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SavingReportResource extends Resource
{
    protected static ?string $model = Saving::class;
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Saving Report';
    protected static string | UnitEnum | null $navigationGroup = 'Savings';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $recordTitleAttribute = 'member.name';

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
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
                    })
                    ->default(now()->year),
                SelectFilter::make('member_id')
                    ->label('Member')
                    ->searchable()
                    ->options(fn (): array => Member::query()->where('status', '=', 'active')->pluck('name', 'slug')->all())
            ] , layout: FiltersLayout::AboveContent)
            ->defaultGroup('member.name')
            ->groupsOnly()
            ->filtersFormColumns(3)
            ->deselectAllRecordsWhenFiltered(false)
            ->hiddenFilterIndicators()
            ->filtersResetActionPosition(FiltersResetActionPosition::Footer)
            ->persistFiltersInSession()
            ->deferFilters(true)
            ->deferLoading(true)
            ->persistSortInSession()
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
    protected function getHeaderWidgets(): array
    {
        return [
            QuarterlyReportStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSavingReports::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
