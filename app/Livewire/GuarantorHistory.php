<?php

namespace App\Livewire;

use App\Models\Guarantor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;

class GuarantorHistory extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Guarantor::query()
                ->where('member_id', '=', auth()->user()->member->slug)
                ->where('status', '=','approved')
                ->whereHas('loan.loanAmorts', function ($query) {
                    $query->where('status', '!=', 'paid')->where('status', '!=', 'complete');
                })
            )
            ->paginated(false)
            ->deferLoading(true)
            ->columns([
                Stack::make([
                    ImageColumn::make('loan.member.name')
                        ->label('Member')
                        ->getStateUsing(function ($record) {
                            // Get the name from the relationship
                            $name = $record->loan?->member?->name ?? 'User';

                            // Generate a URL for the UI Avatars API
                            return "https://ui-avatars.com/api/?name=" . urlencode($name) . "&color=FFFFFF&background=09090b";
                        })
                        ->alignCenter()
                        ->circular(),
                    TextColumn::make('status')
                        ->formatStateUsing(fn ($state) => ucfirst($state))
                        ->size(TextSize::Large)
                        ->alignCenter()
                        ->badge(),
                    TextColumn::make('loan.member.name')
                        ->label('Loan by')
                        ->weight(FontWeight::ExtraBold)
                        ->size(TextSize::Large)
                        ->alignCenter()
                        ->searchable(),
                    TextColumn::make('loan.amount')
                        ->prefix('Loan amount: ')
                        ->size(TextSize::Small)
                        ->alignCenter()
                        ->numeric(),
                    TextColumn::make('loan.terms')
                        ->prefix('Loan period: ')
                        ->suffix(' Month(s)')
                        ->size(TextSize::Small)
                        ->alignCenter()
                        ->numeric(),
                    TextColumn::make('amount')
                        ->prefix('Guaranteed amount: ')
                        ->size(TextSize::Small)
                        ->alignCenter()
                        ->numeric(),
                    TextColumn::make('pending')
                        ->prefix('Pending loan amount: ')
                        ->size(TextSize::Small)
                        ->getStateUsing(function ($record) {
                            return $record->loan->loanAmorts()
                                ->where('status', '!=', 'paid')
                                ->where('status', '!=', 'complete')
                                ->sum('principal');
                        })
                        ->color('danger')
                        ->weight(FontWeight::Bold)
                        ->alignCenter()
                        ->numeric(),

                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
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

    #[title('Guarantor History')]
    public function render(): View
    {
        return view('livewire.guarantor-history');
    }
}
