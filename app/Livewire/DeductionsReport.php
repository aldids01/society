<?php

namespace App\Livewire;

use App\Filament\Exports\MemberExporter;
use App\Models\Member;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ExportAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class DeductionsReport extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;


    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->query(fn (): Builder => Member::query()
                ->where('status', 'active')
                ->with(['loanAmorts' => function ($query) {
                    $query->where('period', date('F'))
                        ->where('annual', date('Y'))
                        ->where('status', '=', 'pending');
                }, 'grainAmorts' => function ($query) {
                    $query->where('period', date('F'))
                        ->where('annual', date('Y'))
                        ->where('status', '=', 'pending');
                }])->orderBy('name', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->formatStateUsing(fn($state) => strtoupper($state)),
                TextColumn::make('saving'),
                TextColumn::make('loanAmorts.interest')
                    ->label('Loan Interest')
                    ->numeric()
                    ->placeholder(0)
                    ->formatStateUsing(fn($state) => $state ?? '0'),
                TextColumn::make('loanAmorts.principal')
                    ->label('Loan Principal')
                    ->numeric()
                    ->placeholder(0)
                    ->formatStateUsing(fn($state) => $state ?? '0'),
                TextColumn::make('grainAmorts.interest')
                    ->label('Grain Interest')
                    ->numeric()
                    ->placeholder(0)
                    ->formatStateUsing(fn($state) => $state ?? '0'),
                TextColumn::make('grainAmorts.principal')
                    ->label('Grain Principal')
                    ->numeric()
                    ->placeholder(0)
                    ->formatStateUsing(fn($state) => $state ?? '0'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make('export')
                    ->label('Export Deduction')
                    ->exporter(MemberExporter::class)
                    ->requiresConfirmation()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalIcon('heroicon-o-arrow-down-tray')
                    ->modalHeading(date('F, Y') . ' Monthly Deduction')
                    ->modalDescription('Monthly Deduction for members, this file contains saving, loan interest, loan pricipal, grain interest and grain pricipal for members to be deducted from their salary this month.')
                    ->color('primary')
                    ->filename(date('F, Y') . ' MONTHLY DEDUCTION')
                    ->slideOver(),
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
    #[title('Deductions Report')]
    public function render(): View
    {
        return view('livewire.deductions-report');
    }
}
