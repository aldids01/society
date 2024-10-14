<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Filament\Resources\ApplicantResource\RelationManagers;
use App\Models\Applicant;
use App\Models\GrainAmort;
use App\Models\LoanAmort;
use App\Models\Saving;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-m-user-group';
    protected static ?string $navigationGroup = 'People';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('staff_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('gender')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kin_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kin_relationship')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kin_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kin_address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('saving')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kin_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kin_relationship')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kin_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kin_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('saving')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'warning' => 'inactive',
                        'primary' => 'active',
                        'danger' => 'withdrawn',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'inactive',
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-exclamation-circle' => 'withdrawn',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
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
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'withdrawn' => 'Withdrawn',
                    ])
                    ->default('active')
            ])
            ->defaultSort('name')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make()
                        ->visible(fn($record) => $record->trashed()), // Visible only for trashed records

                    Tables\Actions\RestoreAction::make()
                        ->visible(fn($record) => $record->trashed()),
                    Tables\Actions\Action::make('withdrawal')
                        ->label('Withdraw Applicant')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => 'Withdraw for ' . $record->name)
                        ->form(function ($record) {
                            return [
                                TextInput::make('savesing')
                                    ->label('Total Saving')
                                    ->default(fn() =>Saving::where('applicant_id', $record->staff_id)->sum('total'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly()
                                    ->required(),
                                TextInput::make('loan')
                                    ->label('Loan Balance')
                                    ->default(fn() =>LoanAmort::where('loan_owner', $record->staff_id)->where('status', '=', 'pending')->sum('principal'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                TextInput::make('grain')
                                    ->label('Grain Balance')
                                    ->default(fn() => GrainAmort::where('grain_owner', $record->staff_id)->where('status', '=', 'pending')->sum('principal'))
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),
                                TextInput::make('pay')
                                    ->label('Pay Off')
                                    ->default(function () use ($record) {
                                        $savesing = Saving::where('applicant_id', $record->staff_id)->sum('total');
                                        $loan = LoanAmort::where('loan_owner', $record->staff_id)->where('status', '=', 'pending')->sum('principal');
                                        $grain = GrainAmort::where('grain_owner', $record->staff_id)->where('status', '=', 'pending')->sum('principal');

                                        return $savesing - ($loan + $grain);
                                    })
                                    ->numeric()
                                    ->prefix('NGN')
                                    ->readOnly(),

                            ];
                        })
                        ->action(function ($record, array $data) {
                            LoanAmort::where('loan_owner', $record->staff_id)->where('status', '=', 'pending')->delete();
                            GrainAmort::where('grain_owner', $record->staff_id)->where('status', '=', 'pending')->delete();
                            Saving::where('applicant_id', $record->staff_id)->delete();

                            $paid = $data['savesing'] - ($data['loan'] + $data['grain']);

                            Saving::create([
                                'applicant_id' => $record->staff_id,
                                'annual' => date('Y'),
                                date('F') => $paid,
                            ]);
                        })
                        ->icon('heroicon-m-banknotes')
                        ->visible(fn($record)=>$record->status === 'active')
                        ->slideOver(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('changeStatus')
                        ->label('Change Status')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'withdrawn' => 'Withdrawn',
                                ])
                                ->required()
                                ->label('Select Status'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        })
                        ->requiresConfirmation()
                        ->color('primary')
                        ->icon('heroicon-o-arrows-right-left'),
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
            'index' => Pages\ListApplicants::route('/'),
            'create' => Pages\CreateApplicant::route('/create'),
            'view' => Pages\ViewApplicant::route('/{record}'),
            'edit' => Pages\EditApplicant::route('/{record}/edit'),
        ];
    }
}
