<?php

namespace App\Livewire;

use App\Models\GrainAmort;
use App\Models\LoanAmort;
use App\Models\Member;
use App\Models\Saving;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class MembersReport extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
            ->query(fn (): Builder => Member::query())
            ->columns([
                TextColumn::make('name')
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->searchable(),
                TextColumn::make('gender')
                    ->badge(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'withdrawn' => 'Withdrawn'
                    ]),
                SelectFilter::make('slug')
                    ->label('Member')
                    ->searchable()
                    ->options(fn (): array => Member::query()->pluck('name', 'slug')->all())
            ] , layout: FiltersLayout::AboveContent)
            ->defaultSort('name', 'asc')
            ->deselectAllRecordsWhenFiltered(false)
            ->hiddenFilterIndicators()
            ->filtersResetActionPosition(FiltersResetActionPosition::Footer)
            ->persistFiltersInSession()
            ->deferFilters(true)
            ->deferLoading(true)
            ->persistSortInSession()
            ->headerActions([
                CreateAction::make()
                ->url(route('members.create'))
            ])
            ->recordActions([
                DeleteAction::make()
                    ->button()->size(Size::ExtraSmall),
                ForceDeleteAction::make()
                    ->button()->size(Size::ExtraSmall),
                Action::make('activate')
                    ->button()->size(Size::ExtraSmall)
                    ->requiresConfirmation()
                    ->action(function (Member $member) {
                        $member->update(['status' => 'active']);

                        Notification::make()
                            ->title('Member activated')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status == 'inactive' || $record->status == 'withdrawn'),
                Action::make('deactivate')
                    ->button()->size(Size::ExtraSmall)
                    ->requiresConfirmation()
                    ->action(function (Member $member) {
                        $member->update(['status' => 'inactive']);

                        Notification::make()
                            ->title('Member deactivated')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status == 'active'),
                Action::make('withdraw')
                    ->button()->size(Size::ExtraSmall)
                    ->requiresConfirmation()
                    ->schema([
                        TextInput::make('saving')
                            ->label('Total Saving')
                            ->default(fn($record) => Saving::query()->where('member_id', $record->slug)->sum('total')),
                        TextInput::make('loan')
                            ->label('Loan Balance')
                            ->default(fn($record) => LoanAmort::query()->where('member_id', $record->slug)->where('status', '!=', 'paid')->where('status', '!=', 'complete')->sum('principal')),
                        TextInput::make('grain')
                            ->label('Grain Balance')
                            ->default(fn($record) => GrainAmort::query()->where('member_id', $record->slug)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->sum('principal')),
                        TextInput::make('payoff')
                            ->label('Pay Off')
                            ->default(
                                fn($record) =>
                                abs(Saving::query()->where('member_id', $record->slug)->sum('total')
                                - (LoanAmort::query()->where('member_id', $record->slug)->where('status', '!=', 'paid')->where('status', '!=', 'complete')->sum('principal')
                                + GrainAmort::query()->where('member_id', $record->slug)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->sum('principal')))
                            ),
                    ])
                    ->action(function ($record, array $data) {
                        LoanAmort::query()->where('member_id', $record->slug)->where('status', '!=', 'paid')->where('status', '!=', 'complete')->update(['status' => 'paid']);
                        GrainAmort::query()->where('member_id', $record->slug)->where('status', '!=', 'pai')->where('status', '!=', 'completed')->update(['status' => 'paid']);
                        Saving::query()->where('member_id', $record->slug)->forceDelete();


                        Saving::create([
                            'member_id' => $record->slug,
                            'annual' => date('Y'),
                            date('F') => $data['payoff'],
                            'status' => 'withdrawn'
                        ]);

                        $record->update(['status' => 'withdrawn']);

                        Notification::make()
                            ->title($record->name . ' withdrawn successfully')
                            ->body('Selected member was withdrawn successfully')
                            ->send();
                    })
                    ->icon('heroicon-s-x-circle')
                    ->slideOver()
                    ->visible(fn($record) => $record->status == 'active'),
                Action::make('saving')
                    ->button()->size(Size::ExtraSmall)
                    ->color('info')
                    ->url(fn($record) => route('member.saving', ['member' => $record->slug ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }


    #[Layout('components.layouts.admin')]
    #[title('Members Report')]
    public function render(): View
    {
        return view('livewire.members-report');
    }
}
