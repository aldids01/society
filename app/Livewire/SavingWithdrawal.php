<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\LoanAmort;
use App\Models\Saving;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

class SavingWithdrawal extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $record = LoanAmort::query()->where('member_id', '=', auth()->user()->member->slug)->where('status', '!=', 'paid')->where('status', '!=', 'complete')->sum('principal');
        return $schema
            ->components([
                TextInput::make('savesing')
                    ->label('Total Saving')
                    ->default(fn() => Saving::query()->where('member_id', '=', auth()->user()->member->slug)->sum(DB::raw('January + February + March + April + May + June + July + August + September + October + November + December')))
                    ->numeric()
                    ->prefix('NGN')
                    ->readOnly()
                    ->required(),
                TextInput::make('loan')
                    ->label('Loan Amount')
                    ->default($record)
                    ->numeric()
                    ->live(debounce: 500)
                    ->prefix('NGN'),
                TextInput::make('charges')
                    ->label('3% Charges')
                    ->default(function () use ($record) {
                        $per = 0.03;
                        $loan = $record;
                        return $per * $loan;
                    })
                    ->numeric()
                    ->prefix('NGN')
                    ->readOnly(),
                TextInput::make('new')
                    ->label('Saving Balance')
                    ->prefix('NGN')
                    ->default(function () use ($record) {
                        $savesing = Saving::query()->where('member_id', auth()->user()->member->slug)->sum(DB::raw('January + February + March + April + May + June + July + August + September + October + November + December'));
                        $loan = $record;
                        $per = 0.03;
                        $charges = $per * $loan;
                        return $savesing - ($loan + $charges);
                    })
                    ->numeric()
                    ->prefix('NGN')
                    ->readOnly(),
            ])
            ->statePath('data')
            ->model(Loan::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Loan::create($data);

        $this->form->model($record)->saveRelationships();

        $term = $record->where('status', '!=', 'paid')->where('status', '!=', 'complete')->where('loan_id', '=', $record->loan->slug)->count();
        $record->where('loan_id', $record->loan_id)->where('status', '!=', 'paid')->where('status', '!=', 'complete')->forceDelete();
        $paid = $data['charges'] + $data['loan'];
        $bal = $data['loan'] - $paid;
        LoanAmort::create([
            'loan_id' => $record->loan_id,
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
            $rate = $record->loan->rate / 100 / 12;
            $payment = $startBalance * ($rate / (1 - pow(1 + $rate, -$terms)));

            $remainingBalance = $startBalance;

            for ($i = 0; $i < $terms; $i++) {
                $interestPayment = $remainingBalance * $rate;
                $principalPayment = $payment - $interestPayment;
                $paymentDate = $startDate->copy()->addMonths($i);
                $endBalance = $remainingBalance - $principalPayment;
                LoanAmort::create([
                    'loan_id' => $record->loan_id,
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
    }

    #[title('Saving Withdrawal')]
    public function render(): View
    {
        return view('livewire.saving-withdrawal');
    }
}
