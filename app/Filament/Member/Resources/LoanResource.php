<?php

namespace App\Filament\Member\Resources;

use Filament\Forms;
use App\Models\Loan;
use Filament\Tables;
use App\Models\Saving;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\ApprovedLoan;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Member\Resources\LoanResource\Pages;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-m-shopping-bag';
    protected static ?string $navigationLabel = 'Loan Request';
    public static function getModelLabel(): string
    {
        return Auth::user()->name . ' Loans';
    }
    protected static ?string $navigationGroup = 'Request';


    public static function form(Form $form): Form
    {
        $user = Auth::user()->applicant;
        $savingsSum = Saving::where('applicant_id', '=', $user->staff_id)->sum('total');
        return $form
            ->schema([
                Section::make('Loan Information')->schema([
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->default(Str::random(8))
                        ->readOnly()
                        ->maxLength(255),
                    Forms\Components\Select::make('applicant_id')
                        ->relationship('applicant', 'name')
                        ->default($user->staff_id)
                        ->required()
                        ->disabled()
                        ->dehydrated(),
                    Forms\Components\ToggleButtons::make('guarantor_type')
                        ->inline()
                        ->options([
                            'applicant' => 'Membership',
                            'collateral' => 'Collateral',
                            'self guaranteed' => 'Self Guaranteed',
                        ])
                        ->icons([
                            'applicant' => 'heroicon-s-user-group',
                            'collateral' => 'heroicon-s-document-text',
                            'self guaranteed' => 'heroicon-s-user',
                        ])
                        ->default('self guaranteed')
                        ->reactive()
                        ->columnSpan(2)
                        ->required(),
                    Forms\Components\TextInput::make('saved')
                        ->required()
                        ->readOnly()
                        ->default($savingsSum)
                        ->prefix('NGN'),
                    Forms\Components\ToggleButtons::make('status')
                        ->inline()
                        ->options([
                            'pending' => 'Pending',
                            'checked' => 'Checked',
                            'rejected' => 'Rejected',
                            'approved' => 'Approved',
                            'disbursed' => 'Disbursed',
                        ])
                        ->icons([
                            'pending' => 'heroicon-s-x-mark',
                            'checked' => 'heroicon-s-check',
                            'rejected' => 'heroicon-s-x-circle',
                            'approved' => 'heroicon-s-check-badge',
                            'disbursed' => 'heroicon-s-truck',
                        ])
                        ->default('pending')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->columnSpan(2),
                        Forms\Components\TextInput::make('rate')
                            ->required()
                            ->prefix('%')
                            ->readOnly()
                            ->numeric(),
                    Grid::make()->schema([
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->prefix('NGN')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) use ($savingsSum) {
                                $rate = self::calculateRate($state, $savingsSum);
                                $set('rate', $rate);
                            }),
                        Forms\Components\DatePicker::make('start_date')
                            ->native(false)
                            ->displayFormat('jS F, Y')
                            ->required(),
                        Forms\Components\Select::make('terms')
                            ->required()
                            ->options(
                                collect(range(1, 12))->mapWithKeys(function ($month) {
                                    return [$month => "$month " . Str::plural('Month', $month)];
                                })->toArray()
                            ),

                    ])->columns(3),
                ])->columns(4),
                Repeater::make('guarantors')
                ->label('Guarantors Information')
                ->columnSpanFull()
                ->addActionLabel('Add another Guarantor')
                ->relationship('guarantors')
                ->schema([
                    Forms\Components\Select::make('guarantor_name')
                        ->relationship('applicant', 'name', function($query){
                            return $query->where('status', 'active');
                        })
                        ->required()
                        ->preload()
                        ->searchable()
                        ->default($user->staff_id)
                        ->searchPrompt('Search applicant by their name, or Staff ID')
                        ->searchingMessage('Searching applicant...')
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->label('Guarantor Fullname'),
                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->label('Guaranteed Amount')
                        ->default($savingsSum)
                        ->prefix('NGN')
                        ->required(),
                ])->columns(2)
                ->visible(fn ($get) => $get('guarantor_type') !== 'collateral'),
                Repeater::make('Amortization')
                    ->deletable(false)
                    ->addable(false)
                    ->columnSpanFull()
                    ->label('Loan Amortization')
                    ->relationship('loanAmort')
                    ->schema([
                        Forms\Components\TextInput::make('annual')
                            ->label('Year')
                            ->disabled(),
                        Forms\Components\TextInput::make('period')
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\TextInput::make('interest')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('principal')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('payment')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('start_balance')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('end_balance')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->formatStateUsing(fn($state)=>ucwords($state))
                            ->disabled(),
                    ])->columns(8),
                Repeater::make('approvals')
                    ->deletable(false)
                    ->addable(false)
                    ->columnSpanFull()
                    ->label('Official Use Only')
                    ->relationship('approvals')
                    ->minItems(1)
                    ->schema([
                        Forms\Components\Select::make('checkedby')
                            ->relationship('check', 'name')
                            ->default(null)
                            ->label('Checked By')
                            ->disabled(),
                        Forms\Components\DatePicker::make('checkeddate')
                            ->label('Checked Date')
                            ->native(false)
                            ->displayFormat('F jS, Y g:ia')
                            ->locale('us')
                            ->disabled(),
                        Forms\Components\Select::make('approvedby')
                            ->relationship('approve', 'name')
                            ->default(null)
                            ->label('Approved By')
                            ->disabled(),
                        Forms\Components\DatePicker::make('approveddate')
                            ->native(false)
                            ->label('Approved Date')
                            ->displayFormat('F jS, Y g:ia')
                            ->locale('us')
                            ->disabled(),
                        Forms\Components\Select::make('disbursedby')
                            ->relationship('disburse', 'name')
                            ->default(null)
                            ->label('Disbursed By')
                            ->disabled(),
                        Forms\Components\DatePicker::make('disburseddate')
                            ->label('Disbursed Date')
                            ->native(false)
                            ->displayFormat('F jS, Y g:ia')
                            ->locale('us')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }
    protected static function calculateRate($amount, $savings): int
    {
        if ($amount <= 4 * $savings AND $amount > 3 * $savings) {
            return 24;
        } elseif ($amount <= 3 * $savings AND $amount > 2 * $savings) {
            return 18;
        } elseif ($amount <= 2 * $savings AND $amount > 1 * $savings) {
            return 12;
        } elseif ($amount <= 1 * $savings) {
            return 6;
        } else {

            Notification::make()
                ->title('Excessive Loan Amount')
                ->body('The loan amount you enter is beyond the constitution of this society, please kindly reduce your loan amount to equals your saving or four (4) times of your saving. Thanks.')
                ->danger()
                ->color('danger')
                ->persistent()
                ->send();
            return 0;
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('applicant.name')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('guarantor_type')
                    ->formatStateUsing(fn (string $state): string => ucwords($state)),
                Tables\Columns\TextColumn::make('saved')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('terms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'checked',
                        'primary' => 'disbursed',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-s-x-mark'=> 'pending' ,
                        'heroicon-s-check'=> 'checked' ,
                        'heroicon-s-x-circle' => 'rejected',
                        'heroicon-s-check-badge' => 'approved',
                        'heroicon-s-truck' => 'disbursed',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('applicant_id')
                    //->relationship('applicant', 'name')
                    ->options([Auth::user()->applicant->staff_id => Auth::user()->applicant->name])
                    ->label('Applicant')
                    ->selectablePlaceholder(false)
                    ->default(Auth::user()->applicant->staff_id),
            ])->hiddenFilterIndicators()
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn($record)=>$record->status === 'pending'),
                    Tables\Actions\Action::make('Disbursed')
                            ->label('Disbursed')
                            ->action(function ($record) {
                                $record->update(['status' => 'disbursed']);
                                $approvedLoan = ApprovedLoan::where('loan_id', $record->slug)->first();
                                if ($approvedLoan) {
                                    $approvedLoan->update([
                                        'disbursedby' => Auth::user()->applicant->staff_id,
                                        'disburseddate' => now(),
                                    ]);
                                }
                            })
                            ->requiresConfirmation()
                            ->color('primary')
                            ->icon('heroicon-s-truck')
                            ->visible(fn($record)=>$record->status === 'approved'),
                    Tables\Actions\Action::make('approve')
                            ->label('Approve')
                            ->action(function ($record) {
                                $record->update(['status' => 'approved']);
                                $approvedLoan = ApprovedLoan::where('loan_id', $record->slug)->first();
                                if ($approvedLoan) {
                                    $approvedLoan->update([
                                        'approvedby' => Auth::user()->applicant->staff_id,
                                        'approveddate' => now(),
                                    ]);
                                }
                            })
                            ->requiresConfirmation()
                            ->color('success')
                            ->icon('heroicon-s-check')
                            ->visible(fn($record)=>$record->status === 'checked'),
                    Tables\Actions\Action::make('reject')
                            ->label('Reject')
                            ->action(function ($record) {
                                $record->update(['status' => 'rejected']);
                                $approvedLoan = ApprovedLoan::where('loan_id', $record->slug)->first();
                                if ($approvedLoan) {
                                    $approvedLoan->update([
                                        'approvedby' => Auth::user()->applicant->staff_id,
                                        'approveddate' => now(),
                                    ]);
                                }
                            })
                            ->requiresConfirmation()
                            ->color('danger')
                            ->icon('heroicon-s-x-circle')
                            ->visible(fn($record)=>$record->status === 'checked'),
                    Tables\Actions\Action::make('checked')
                            ->label('Checked')
                            ->action(function ($record) {
                                $record->update(['status' => 'checked']);
                                $approvedLoan = ApprovedLoan::where('loan_id', $record->slug)->first();
                                if ($approvedLoan) {
                                    $approvedLoan->update([
                                        'checkedby' => Auth::user()->applicant->staff_id,
                                        'checkeddate' => now(),
                                    ]);
                                }
                            })
                            ->requiresConfirmation()
                            ->color('info')
                            ->icon('heroicon-s-pencil-square')
                            ->visible(fn($record)=>$record->status === 'pending'),
                    Tables\Actions\Action::make('exportPdf')
                            ->label('Download PDF')
                            ->icon('heroicon-s-arrow-down-tray')
                            ->action(function ($record) {
                                // Generate PDF
                                $pdf = Pdf::loadView('pdf.loan-export', ['record' => $record])->setPaper('a4', 'portrait');

                                // Return the download response
                                return response()->streamDownload(
                                    fn () => print($pdf->stream()),
                                    "loan-form-{$record->applicant->name}.pdf"
                                );
                            })
                            ->color('primary')
                            ->requiresConfirmation(),
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
