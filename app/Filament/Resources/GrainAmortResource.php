<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Saving;
use Filament\Forms\Form;
use App\Models\Applicant;
use App\Models\GrainAmort;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GrainAmortResource\Pages;
use App\Filament\Resources\GrainAmortResource\RelationManagers;

class GrainAmortResource extends Resource
{
    protected static ?string $model = GrainAmort::class;
    protected static ?string $modelLabel = 'Grain Schedules';
    protected static ?string $navigationIcon = 'heroicon-m-presentation-chart-bar';
    protected static ?string $navigationGroup = 'Finance';
    public $totalSavings;

    public function mount($record)
    {
        $this->totalSavings = Saving::where('applicant_id', $record->loan_owner)->sum('total');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('grain_id')
                    ->relationship('grain', 'id'),
                Forms\Components\TextInput::make('grain_owner')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('annual'),
                Forms\Components\TextInput::make('period')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('interest')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('principal')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('payment')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('start_balance')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('end_balance')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant.name')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('annual'),
                Tables\Columns\TextColumn::make('period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interest')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('principal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('start_balance')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('end_balance')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->money('NGN')),
                Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'overdue',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'pending',
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-exclamation-circle' => 'overdue',
                    ])
                    ->formatStateUsing(fn (string $state): string =>ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ])
                    ->default('pending'),
                SelectFilter::make('period')
                    ->options(function () {
                        return collect(range(1, 12))->mapWithKeys(function ($month) {
                            $monthName = \Carbon\Carbon::create()->month($month)->format('F');
                            return [$monthName => $monthName];
                        })->toArray();
                    })
                    ->default(now()->format('F')),
                SelectFilter::make('annual')
                    ->options(function () {
                        $years = range(now()->year, now()->year + 10);
                        return array_combine($years, $years);
                    })
                    ->default(now()->year),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->url(fn ($record) => url("/member/grains/{$record->grain->id}"))
                        ->label('View Loan'),
                    Tables\Actions\Action::make('shift')
                        ->label('Shift Grain')
                        ->requiresConfirmation()
                        ->icon('heroicon-m-pencil-square')
                        ->form([
                            DatePicker::make('start_date')
                                ->native(false)
                                ->displayFormat('jS F Y')
                                ->placeholder('Next Payment Date')
                                ->required()
                                ->locale('us')
                                ->label('Next Payment Start from')
                        ])
                        ->action(function ($record, array $data) {
                            GrainAmort::where('grain_id', $record->grain_id)->where('status', 'pending')->delete();
                            GrainAmort::create([
                                'grain_id' => $record->grain_id,
                                'grain_owner' => $record->grain_owner,
                                'annual' => $record->annual,
                                'period' => $record->period,
                                'interest' => $record->interest,
                                'principal' => 0,
                                'payment' => $record->interest,
                                'start_balance' => $record->start_balance,
                                'end_balance' => $record->start_balance,
                            ]);

                            $startDate = Carbon::parse($data['start_date']);
                            $startBalance = $record->start_balance;
                            $terms = $record->grain->terms;
                            $rate = $record->grain->rate / 100 / $terms;
                            $totalInterest = $startBalance * $rate * $terms;

                            $payment = ($startBalance + $totalInterest) / $terms;

                            $fixedInterest = $totalInterest / $terms;

                            $remainingBalance = $startBalance;

                            for ($i = 0; $i < $terms; $i++) {
                                $principalPayment = $payment - $fixedInterest;

                                $paymentDate = $startDate->copy()->addMonths($i);
                                $endBalance = $remainingBalance - $principalPayment;
                                GrainAmort::create([
                                    'grain_id' => $record->grain_id,
                                    'grain_owner' => $record->grain_owner,
                                    'annual' => $paymentDate->format('Y'),
                                    'period' => $paymentDate->format('F'),
                                    'interest' => $fixedInterest,
                                    'principal' => $principalPayment,
                                    'payment' => $payment,
                                    'start_balance' => $remainingBalance,
                                    'end_balance' => $endBalance,
                                ]);
                                $remainingBalance = $endBalance;
                            }

                        })
                        ->visible(fn($record)=>$record->status !== 'paid')
                        ->slideOver(),
                    Tables\Actions\Action::make('payment')
                        ->label('Extra Payment')
                        ->requiresConfirmation()
                        ->icon('heroicon-m-credit-card')
                        ->form(function ($record) {
                            return [
                                TextInput::make('start_balance')
                                    ->label('Loan Balance')
                                    ->placeholder('Loan Balance')
                                    ->default($record->start_balance)
                                    ->numeric()
                                    ->disabled(),
                                TextInput::make('paid')
                                    ->label('Amount')
                                    ->placeholder('Amount Payable')
                                    ->numeric()
                                    ->required(),
                                DatePicker::make('start_date')
                                    ->native(false)
                                    ->placeholder('Next Payment Date')
                                    ->displayFormat('jS F Y')
                                    ->locale('us')
                                    ->label('Next Payment Start from')
                                    ->required(),
                            ];
                        })
                        ->action(function ($record, array $data) {
                            GrainAmort::where('grain_id', $record->grain_id)->where('status', 'pending')->delete();

                            $paid_bal = $record->start_balance - $data['paid'];
                            GrainAmort::create([
                                'grain_id' => $record->grain_id,
                                'grain_owner' => $record->grain_owner,
                                'annual' => $record->annual,
                                'period' => $record->period,
                                'interest' => 0,
                                'principal' => $data['paid'],
                                'payment' => $data['paid'],
                                'start_balance' => $record->start_balance,
                                'end_balance' => $paid_bal,
                                'status' => 'paid',
                            ]);

                            $startDate = Carbon::parse($data['start_date']);
                            $startBalance = $paid_bal;
                            $terms = $record->grain->terms;
                            $rate = $record->grain->rate / 100 / $terms;
                            $totalInterest = $startBalance * $rate * $terms;
                            $fixedInterest = $totalInterest / $terms;
                            $payment = ($startBalance + $totalInterest) / $terms;
                            $remainingBalance = $startBalance;

                            for ($i = 0; $i < $terms; $i++) {

                                $principalPayment = $payment - $fixedInterest;
                                $paymentDate = $startDate->copy()->addMonths($i);
                                $endBalance = $remainingBalance - $principalPayment;
                                GrainAmort::create([
                                    'grain_id' => $record->grain_id,
                                    'grain_owner' => $record->grain_owner,
                                    'annual' => $paymentDate->format('Y'),
                                    'period' => $paymentDate->format('F'),
                                    'interest' => $fixedInterest,
                                    'principal' => $principalPayment,
                                    'payment' => $payment,
                                    'start_balance' => $remainingBalance,
                                    'end_balance' => $endBalance,
                                ]);

                                $remainingBalance = $endBalance;
                            }

                        })
                        ->visible(fn($record)=>$record->status !== 'paid')
                        ->slideOver(),
                    Tables\Actions\Action::make('stop')
                        ->label('Stop Grain')
                        ->requiresConfirmation()
                        ->action(function($record){
                            GrainAmort::where('grain_id', $record->grain_id)->where('status', 'pending')->delete();
                        })
                        ->icon('heroicon-m-x-mark')
                        ->visible(fn($record)=>$record->status !== 'paid')
                        ->slideOver(),
                    Tables\Actions\Action::make('savings')
                        ->label('Saving Payout')
                        ->requiresConfirmation()
                        ->form(function ($record) {
                            return [
                                TextInput::make('savesing')
                                    ->label('Total Saving')
                                    ->default(fn() =>Saving::where('applicant_id', $record->grain_owner)->sum('total'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('loan')
                                    ->label('Grain Balance')
                                    ->default($record->start_balance)
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                TextInput::make('charges')
                                    ->label('3% Charges')
                                    ->default(function () use ($record) {
                                        $per = 0.03;
                                        $loan = $record->start_balance;
                                        return $per * $loan;
                                    })
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                TextInput::make('new')
                                    ->label('Saving Balance')
                                    ->default(function () use ($record) {
                                        $savesing = Saving::where('applicant_id', $record->grain_owner)->sum('total');
                                        $loan = $record->start_balance;
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
                            GrainAmort::where('grain_id', $record->grain_id)->where('status', 'pending')->delete();
                            $paid = $data['charges'] + $data['loan'];
                            GrainAmort::create([
                                'grain_id' => $record->grain_id,
                                'grain_owner' => $record->grain_owner,
                                'annual' => $record->annual,
                                'period' => $record->period,
                                'interest' => $data['charges'],
                                'principal' => $data['loan'],
                                'payment' => $paid,
                                'start_balance' => $record->start_balance,
                                'end_balance' => 0,
                                'status' => 'paid',
                            ]);

                            Saving::where('applicant_id', $record->grain_owner)->delete();

                            Saving::create([
                                'applicant_id' => $record->grain_owner,
                                'annual' => date('Y'),
                                date('F') => $data['new'],
                            ]);
                        })
                        ->icon('heroicon-m-banknotes')
                        ->visible(fn($record)=>$record->status !== 'paid')
                        ->slideOver(),
                    Tables\Actions\Action::make('transfer')
                        ->label('Transfer Grain')
                        ->requiresConfirmation()
                        ->form([
                            Select::make('applicant')
                                ->options(Applicant::query()->where('status', 'active')->pluck('name', 'staff_id')->toArray())
                                ->required()
                                ->label('Transfer Grain to')
                        ])
                        ->action(function ($record, $data) {

                                $loans = GrainAmort::where('status', 'pending')->where('grain_id', $record->grain_id)->get();
                                foreach($loans as $loan){
                                    $loan->update([
                                        'grain_owner' => $data['applicant'],
                                    ]);
                                }
                        })
                        ->icon('heroicon-m-arrow-right-circle')
                        ->visible(fn($record)=>$record->status !== 'paid')
                        ->slideOver(),
                ])
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsPending')
                        ->label('Mark as Pending')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'pending']);
                            });
                            $recipient = Auth::user();

                            Notification::make()
                                ->title('Status changed to Pending')
                                ->body('Selected loan status marked as pending successfully')
                                ->sendToDatabase($recipient);
                        })
                        ->icon('heroicon-o-check-circle'),
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label('Mark as Paid')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'Paid']);
                            });
                            $recipient = Auth::user();

                            Notification::make()
                                ->title('Selected loan approved successfully')
                                ->sendToDatabase($recipient);
                        })
                        ->icon('heroicon-o-check-circle'),

                    Tables\Actions\BulkAction::make('markAsOverdue')
                        ->label('Mark as Overdue')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'Overdue']);
                            });
                        })
                        ->icon('heroicon-o-exclamation-circle'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrainAmorts::route('/'),
            'create' => Pages\CreateGrainAmort::route('/create'),
            'view' => Pages\ViewGrainAmort::route('/{record}'),
            'edit' => Pages\EditGrainAmort::route('/{record}/edit'),
        ];
    }
}
