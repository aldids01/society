<?php

namespace App\Livewire;

use App\Models\Guarantor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Livewire\Attributes\Title;
use Livewire\Component;

class GuarantorRequest extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Guarantor::query()
                ->where('member_id', '=', auth()->user()->member->slug)
                ->where('status', '=','pending')
                ->with(['loan.loanAmorts' => function ($query) {
                    $query->where('status', '!=', 'paid');
                }])
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
                        ->size(TextSize::Medium)
                        ->alignCenter()
                        ->numeric(),
                    TextColumn::make('loan.terms')
                        ->prefix('Loan period: ')
                        ->suffix(' Month(s)')
                        ->size(TextSize::Medium)
                        ->alignCenter()
                        ->numeric(),
                    TextColumn::make('amount')
                        ->prefix('Guaranteed amount: ')
                        ->size(TextSize::Medium)
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
                EditAction::make()->label('Guarantor Approval')
                    ->modalHeading('Guarantor Approval')
                    ->modalDescription('Are you sure you would like to approve?')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-s-pencil')
                    ->modalCancelAction(false)
                    ->modalSubmitActionLabel(fn($record)=>'Approve '.Number::format($record->amount))
                    ->modalWidth(Width::Small)
                    ->action(function ($record) {
                        $record->update(['status'=>'approved']);
                        $amount = Number::format($record->amount);

                        Notification::make()
                            ->success()
                            ->title('Guarantor Approved')
                            ->body("Guaranteed {$record->loan->member->name} with {$amount} has been approved.")
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    #[title('Guarantor Request')]
    public function render(): View
    {
        return view('livewire.guarantor-request');
    }
}
