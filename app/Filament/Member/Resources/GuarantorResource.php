<?php

namespace App\Filament\Member\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Guarantor;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Member\Resources\GuarantorResource\Pages;
use App\Filament\Member\Resources\GuarantorResource\RelationManagers;

class GuarantorResource extends Resource
{
    protected static ?string $model = Guarantor::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Guaranteed';
    protected static ?string $navigationGroup = 'Report';
    public static function getModelLabel(): string
    {
        return Auth::user()->name . ' Guranteed List';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan.applicant.name')
                    ->label('Guaranteed Name'),
                Tables\Columns\TextColumn::make('pending_amortization_principal')
                    ->label('Pending Loan Amount')
                    ->getStateUsing(function ($record) {
                        return Guarantor::where('id', $record->id)
                            ->with(['loan.loanAmort' => function ($query) {
                                $query->where('status', 'pending');
                            }])
                            ->get()
                            ->pluck('loan.loanAmort')
                            ->flatten()
                            ->sum('principal');
                    })
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->label('Guaranteed Amount')
                    ->summarize(Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('guarantor_status')
                ->colors([
                    'warning' => 'pending',
                    'primary' => 'approved',
                    'danger' => 'rejected',
                ])
                ->icons([
                    'heroicon-o-x-circle' => 'pending',
                    'heroicon-o-check-circle' => 'approved',
                    'heroicon-o-exclamation-circle' => 'rejected',
                ])
                ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->filters([
                SelectFilter::make('guarantor_status')
                ->options([
                    'pending' => 'Pending',
                    'approved'=> 'Approved',
                    'rejected' => 'Rejected'
                ]),
                SelectFilter::make('guarantor_name')
                    //->relationship('applicant', 'name')
                    ->options([Auth::user()->applicant->staff_id => Auth::user()->applicant->name])
                    ->label('Applicant')
                    ->selectablePlaceholder(false)
                    ->default(Auth::user()->applicant->staff_id),
            ])->hiddenFilterIndicators()
            ->actions([
                Tables\Actions\ViewAction::make()
                        ->url(fn ($record) => url("/member/loans/{$record->loan->id}"))
                        ->label('View Loan'),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function ($record) {
                        $record->update(['guarantor_status' => 'approved']);
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-s-check')
                    ->visible(fn($record)=>$record->guarantor_status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->action(function ($record) {
                        $record->update(['guarantor_status' => 'rejected']);
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-s-x-circle')
                    ->visible(fn($record)=>$record->guarantor_status === 'pending'),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGuarantors::route('/'),
        ];
    }
}
