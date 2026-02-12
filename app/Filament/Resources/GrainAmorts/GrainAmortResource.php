<?php

namespace App\Filament\Resources\GrainAmorts;

use App\Filament\Resources\GrainAmorts\Pages\ManageGrainAmorts;
use App\Models\GrainAmort;
use App\Models\Member;
use App\Models\Saving;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use UnitEnum;

class GrainAmortResource extends Resource
{
    protected static ?int $navigationSort = 8;

    protected static ?string $model = GrainAmort::class;
    protected static string | UnitEnum | null $navigationGroup = 'Grains';

    protected static ?string $modelLabel = 'Grain Report';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;


    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('grain.amount')
                    ->numeric()
                    ->label('Grain'),
                TextEntry::make('member.name')
                    ->label('Member'),
                TextEntry::make('annual'),
                TextEntry::make('period'),
                TextEntry::make('interest')
                    ->numeric(),
                TextEntry::make('principal')
                    ->numeric(),
                TextEntry::make('payment')
                    ->numeric(),
                TextEntry::make('start_balance')
                    ->numeric(),
                TextEntry::make('end_balance')
                    ->numeric(),
                TextEntry::make('status')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (GrainAmort $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->columns([
                TextColumn::make('member.name'),
                TextColumn::make('annual'),
                TextColumn::make('period'),
                TextColumn::make('interest')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('principal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment')
                    ->numeric()
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
//                TextColumn::make('created_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                TextColumn::make('updated_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                TextColumn::make('deleted_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue', 'complete' => 'Complete'])
                    ->multiple()
                    ->default(['pending', 'overdue'] ),
                SelectFilter::make('period')
                    ->options(function () {
                        return collect(range(1, 12))->mapWithKeys(function ($month) {
                            $monthName = Carbon::create()->month($month)->format('F');
                            return [$monthName => $monthName];
                        })->toArray();
                    })
                    ->default(now()->format('F')),
                SelectFilter::make('annual')
                    ->options(function () {
                        $currentYear = now()->year;
                        $years = range($currentYear - 5, $currentYear + 5);
                        return array_combine($years, $years);
                    })
                    ->default(now()->year),
                SelectFilter::make('member_id')
                    ->label('Member')
                    ->searchable()
                    ->visible(auth()->user()->hasRole('super_admin'))
                    ->options(fn (): array => Member::query()->where('status', '=', 'active')->pluck('name', 'slug')->all())

            ], layout: FiltersLayout::AboveContent)
            ->deselectAllRecordsWhenFiltered(false)
            ->hiddenFilterIndicators()
            ->filtersResetActionPosition(FiltersResetActionPosition::Footer)
            ->persistFiltersInSession()
            ->deferFilters(true)
            ->deferLoading(true)
            ->persistSortInSession()
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->slideOver()
                        ->modalWidth(Width::Medium),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                    Action::make('shift')
                        ->label('Shift Grain')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => 'Shift Grain for ' . $record->member->name)
                        ->modalDescription('When you shift this grain, only interest will be charge for the current month, are you sure you want to do this?')
                        ->icon('heroicon-m-pencil-square')
                        ->modalIcon('heroicon-m-pencil-square')
                        ->schema(function ($record) {
                            $pendingCount = $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain_id)->count();
                            return [
                                TextInput::make('terms')
                                    ->label('Terms')
                                    ->default($pendingCount)
                                    ->numeric()
                                    ->prefix('Months')
                                    ->required(),
                                DatePicker::make('start_date')
                                    ->native(false)
                                    ->displayFormat('jS F Y')
                                    ->placeholder('Next Payment Date')
                                    ->required()
                                    ->locale('us')
                                    ->label('Next Payment Start from')
                            ];
                        })
                        ->action(function ($record, array $data) {
                            $record->where('grain_id', $record->grain_id)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->delete();
                            GrainAmort::create([
                                'grain_id' => $record->grain_id,
                                'member_id' => $record->member_id,
                                'annual' => $record->annual,
                                'period' => $record->period,
                                'interest' => $record->interest,
                                'principal' => 0,
                                'payment' => $record->interest,
                                'start_balance' => $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal'),
                                'end_balance' => $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal'),
                            ]);

                            $startDate = Carbon::parse($data['start_date']);
                            $startBalance = $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal');
                            $terms = $data['terms'];
                            $rate = $record->grain->rate / 100 / 12;
                            $payment = $startBalance * ($rate / (1 - pow(1 + $rate, -$terms)));

                            $remainingBalance = $startBalance;

                            for ($i = 0; $i < $terms; $i++) {
                                $interestPayment = $remainingBalance * $rate;
                                $principalPayment = $payment - $interestPayment;
                                $paymentDate = $startDate->copy()->addMonths($i);
                                $endBalance = $remainingBalance - $principalPayment;
                                GrainAmort::create([
                                    'grain_id' => $record->grain_id,
                                    'member_id' => $record->member_id,
                                    'annual' => $paymentDate->format('Y'),
                                    'period' => $paymentDate->format('F'),
                                    'interest' => $interestPayment,
                                    'principal' => $principalPayment,
                                    'payment' => $payment,
                                    'start_balance' => $remainingBalance,
                                    'end_balance' => $endBalance,
                                ]);
                                $remainingBalance = $endBalance;
                            }
                        })
                        ->visible(fn($record) => $record->status !== 'paid' && auth()->user()->hasRole('super_admin'))
                        ->slideOver(),
                    Action::make('payment')
                        ->label('Extra Payment')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => 'Extra Payment for ' . $record->member->name)
                        ->modalDescription('Extra Payment are directly reducing your loan repayment without interest. Are you sure you want to do this?')
                        ->icon('heroicon-m-credit-card')
                        ->modalIcon('heroicon-m-credit-card')
                        ->schema(function ($record) {
                            $pendingCount = $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->count();
                            return [
                                TextInput::make('terms')
                                    ->label('Terms')
                                    ->default($pendingCount)
                                    ->numeric()
                                    ->prefix('Months')
                                    ->required(),
                                TextInput::make('start_balance')
                                    ->label('Loan Balance')
                                    ->placeholder('Loan Balance')
                                    ->default($record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                TextInput::make('paid')
                                    ->label('Amount')
                                    ->placeholder('Amount Payable')
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->required(),
                                DatePicker::make('start_date')
                                    ->native(false)
                                    ->placeholder('Next Payment Date')
                                    ->displayFormat('jS F Y')
                                    ->closeOnDateSelection()
                                    ->label('Next Payment Start from')
                                    ->required(),
                            ];
                        })
                        ->action(function ($record, array $data) {
                            $record->where('grain_id', $record->grain_id)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->delete();

                            $paid_bal = $data['start_balance'] - $data['paid'];
                            GrainAmort::create([
                                'grain_id' => $record->grain_id,
                                'member_id' => $record->member_id,
                                'annual' => $record->annual,
                                'period' => $record->period,
                                'interest' => 0,
                                'principal' => $data['paid'],
                                'payment' => $data['paid'],
                                'start_balance' => $data['start_balance'],
                                'end_balance' => $paid_bal,
                                'status' => 'paid',
                            ]);

                            if ($paid_bal > 0) {

                                $startDate = Carbon::parse($data['start_date']);
                                $startBalance = $paid_bal;
                                $terms = $data['terms'];
                                $rate = $record->grain->rate / 100 / 12;
                                $payment = $startBalance * ($rate / (1 - pow(1 + $rate, -$terms)));

                                $remainingBalance = $startBalance;

                                for ($i = 0; $i < $terms; $i++) {
                                    $interestPayment = $remainingBalance * $rate;
                                    $principalPayment = $payment - $interestPayment;
                                    $paymentDate = $startDate->copy()->addMonths($i);
                                    $endBalance = $remainingBalance - $principalPayment;
                                    GrainAmort::create([
                                        'grain_id' => $record->grain_id,
                                        'member_id' => $record->member_id,
                                        'annual' => $paymentDate->format('Y'),
                                        'period' => $paymentDate->format('F'),
                                        'interest' => $interestPayment,
                                        'principal' => $principalPayment,
                                        'payment' => $payment,
                                        'start_balance' => $remainingBalance,
                                        'end_balance' => $endBalance,
                                    ]);
                                    $remainingBalance = $endBalance;
                                }
                            }
                        })
                        ->
                        visible(fn($record) => $record->status !== 'paid' && auth()->user()->hasRole('super_admin'))
                        ->slideOver(),
                    Action::make('stop')
                        ->label('Stop Grain')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => 'Stop Grain for ' . $record->member->name)
                        ->modalDescription('When you stop this loan, the member will not be able to make repayments. Are you sure you want to do this?')
                        ->action(function ($record) {
                            $record->where('grain_id', $record->grain_id)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->delete();
                        })
                        ->icon('heroicon-m-x-mark')
                        ->modalIcon('heroicon-m-x-mark')
                        ->visible(fn($record) => $record->status !== 'paid' && auth()->user()->hasRole('super_admin')),
                    Action::make('savings')
                        ->label('Saving Payout')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => 'Saving Payout for ' . $record->member->name)
                        ->schema(function ($record) {
                            return [
                                TextInput::make('savesing')
                                    ->label('Total Saving')
                                    ->default(fn() => Saving::where('member_id', $record->member_id)->sum('total'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('loan')
                                    ->label('Loan Balance')
                                    ->default($record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                TextInput::make('charges')
                                    ->label('3% Charges')
                                    ->default(function () use ($record) {
                                        $per = 0.03;
                                        $loan = $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal');
                                        return $per * $loan;
                                    })
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                TextInput::make('new')
                                    ->label('Saving Balance')
                                    ->prefix('NGN')
                                    ->default(function () use ($record) {
                                        $savesing = Saving::where('member_id', $record->member_id)->sum('total');
                                        $loan = $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal');
                                        $per = 0.03;
                                        $charges = $per * $loan;
                                        return $savesing - ($loan + $charges);
                                    })
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),

                            ];
                        })
                        ->action(function ($record, array $data) {
                            $term = $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->count();
                            $record->where('grain_id', $record->grain_id)->where('status', '!=', 'paid')->where('status', '!=', 'completed')->delete();
                            $paid = $data['charges'] + $data['loan'];
                            $bal = $data['loan'] - $paid;
                            GrainAmort::create([
                                'grain_id' => $record->grain_id,
                                'member_id' => $record->member_id,
                                'annual' => $record->annual,
                                'period' => $record->period,
                                'interest' => $data['charges'],
                                'principal' => $data['loan'],
                                'payment' => $paid,
                                'start_balance' => $bal,
                                'end_balance' => 0,
                                'status' => 'paid',
                            ]);

                            Saving::where('member_id', $record->member_id)->delete();
                            if ($data['new'] > 0) {
                                Saving::create([
                                    'member_id' => $record->member_id,
                                    'annual' => date('Y'),
                                    date('F') => $data['new'],
                                ]);
                            } else {
                                Saving::create([
                                    'member_id' => $record->member_id,
                                    'annual' => date('Y'),
                                    date('F') => 0,
                                ]);
                                $newloan = abs($data['new']);
                                $startDate = Carbon::parse(date('Y-m-d'));
                                $startBalance = $newloan;
                                $terms = $term;
                                $rate = $record->grain->rate / 100 / 12;
                                $payment = $startBalance * ($rate / (1 - pow(1 + $rate, -$terms)));

                                $remainingBalance = $startBalance;

                                for ($i = 0; $i < $terms; $i++) {
                                    $interestPayment = $remainingBalance * $rate;
                                    $principalPayment = $payment - $interestPayment;
                                    $paymentDate = $startDate->copy()->addMonths($i);
                                    $endBalance = $remainingBalance - $principalPayment;
                                    GrainAmort::create([
                                        'grain_id' => $record->grain_id,
                                        'member_id' => $record->member_id,
                                        'annual' => $paymentDate->format('Y'),
                                        'period' => $paymentDate->format('F'),
                                        'interest' => $interestPayment,
                                        'principal' => $principalPayment,
                                        'payment' => $payment,
                                        'start_balance' => $remainingBalance,
                                        'end_balance' => $endBalance,
                                    ]);
                                    $remainingBalance = $endBalance;
                                }
                            }
                        })
                        ->icon('heroicon-m-banknotes')
                        ->modalIcon('heroicon-m-banknotes')
                        ->modalDescription('Saving Payout are using your total saving to reduce or pay off your pending loan. Are you sure you want to do this?')
                        ->visible(fn($record) => $record->status !== 'paid' && auth()->user()->hasRole('super_admin'))
                        ->slideOver(),
                    Action::make('transfer')
                        ->label('Transfer Grain')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => 'Transfer Grain for ' . $record->member->name)
                        ->modalDescription('Transfer your pending loan to another member who agreed to take up the responsibility for the loaned memember. Are you sure you want to to this?')
                        ->schema(function ($record) {
                            return [
                                TextInput::make('loan')
                                    ->label('Loan Balance')
                                    ->default($record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', '=', $record->grain->slug)->sum('principal'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                Select::make('applicant')
                                    ->options(Member::query()->where('status', 'active')->pluck('name', 'slug')->toArray())
                                    ->required()
                                    ->label('Transfer Loan to')
                            ];
                        })
                        ->action(function ($record, $data) {

                            $loans = $record->where('status', '!=', 'paid')->where('status', '!=', 'completed')->where('grain_id', $record->grain_id)->get();
                            foreach ($loans as $loan) {
                                $loan->update([
                                    'member_id' => $data['applicant'],
                                ]);
                            }
                        })
                        ->icon('heroicon-m-arrow-right-circle')
                        ->modalIcon('heroicon-m-arrow-right-circle')
                        ->visible(fn($record) => $record->status !== 'paid' && auth()->user()->hasRole('super_admin'))
                        ->slideOver(),
                ])->button()->size(Size::Small)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    BulkAction::make('paid')
                        ->icon('heroicon-s-check-badge')
                        ->modalIcon('heroicon-s-check-badge')
                        ->modal()
                        ->color('success')
                        ->visible(auth()->user()->hasRole('super_admin'))
                        ->modalDescription('Are you sure you want to do this?')
                        ->modalFooterActionsAlignment(Alignment::Center)
                        ->modalWidth(Width::ExtraSmall)
                        ->modalCancelAction(false)
                        ->label('Mark as Paid')
                        ->action(function(Collection $records): void {
                            $records->each(function($record): void {
                                $record->update(['status' => 'paid']);
                            });

                            Notification::make()
                                ->success()
                                ->title('Successfully Paid')
                                ->body('Successfully Paid Selected loans')
                                ->send();
                        }),
                    BulkAction::make('pending')
                        ->icon('heroicon-s-x-circle')
                        ->modalIcon('heroicon-s-x-circle')
                        ->modalWidth(Width::ExtraSmall)
                        ->modal()
                        ->modalFooterActionsAlignment(Alignment::Center)
                        ->color('primary')
                        ->visible(auth()->user()->hasRole('super_admin'))
                        ->modalDescription('Are you sure you want to do this?')
                        ->modalCancelAction(false)
                        ->label('Mark as Pending')
                        ->action(function(Collection $records): void {
                            $records->each(function($record): void {
                                $record->update(['status' => 'pending']);
                            });

                            Notification::make()
                                ->success()
                                ->title('Successfully')
                                ->body('Selected loans successfully updated to pending')
                                ->send();
                        }),
                    BulkAction::make('overdue')
                        ->icon('heroicon-s-x-circle')
                        ->modalIcon('heroicon-s-x-circle')
                        ->modalWidth(Width::ExtraSmall)
                        ->modal()
                        ->modalFooterActionsAlignment(Alignment::Center)
                        ->color('danger')
                        ->visible(auth()->user()->hasRole('super_admin'))
                        ->modalCancelAction(false)
                        ->label('Mark as Overdue')
                        ->modalDescription('Are you sure you want to do this?')
                        ->action(function(Collection $records): void {
                            $records->each(function($record): void {
                                $record->update(['status' => 'overdue']);
                            });

                            Notification::make()
                                ->success()
                                ->title('Successfully')
                                ->body('Selected loans successfully updated to overdue')
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGrainAmorts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (auth()->user()->hasRole('Member')) {
            $memberSlug = auth()->user()->member->slug;

            return $query->where('member_id', $memberSlug);
        }

        return $query;
    }
}
