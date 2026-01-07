<?php

namespace App\Livewire;

use App\Models\Saving;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Livewire\Attributes\Title;
use Livewire\Component;

class SavingHistory extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;


    public function table(Table $table): Table
    {
        $total = Saving::query()->where('member_id', '=', auth()->user()->member->slug)->sum('total');
        return $table
            ->heading(fn()=> 'Total Saving: ' .Number::format($total, 2).'  ('.ucwords(Number::spell($total)).')')
            ->defaultPaginationPageOption(25)
            ->deferLoading(true)
            ->query(fn (): Builder => Saving::query()->where('member_id', '=', auth()->user()->member->slug))
            ->paginated([10, 25, 50, 100, 'all'])
            ->columns([
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
                //
            ])
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


    #[title('Saving History')]
    public function render(): View
    {
        return view('livewire.saving-history');
    }
}
