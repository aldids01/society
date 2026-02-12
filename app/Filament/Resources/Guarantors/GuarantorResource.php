<?php

namespace App\Filament\Resources\Guarantors;

use App\Filament\Resources\Guarantors\Pages\ManageGuarantors;
use App\Models\Guarantor;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class GuarantorResource extends Resource
{
    protected static ?string $model = Guarantor::class;

    protected static ?int $navigationSort = 12;

    protected static string | UnitEnum | null $navigationGroup = 'Reports';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;


    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('loan.member.name')
                    ->label('Loan By'),
                TextEntry::make('member.name')
                    ->label('Guaranteed By'),
                TextEntry::make('pending')
                    ->label('Pending Loan')
                    ->prefix('NGN')
                    ->getStateUsing(function ($record) {
                        return $record->loan->loanAmorts()
                            ->where('status', '!=', 'paid')
                            ->where('status', '!=', 'complete')
                            ->sum('principal');
                    }),
                TextEntry::make('amount')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan.member.name')
                    ->label('Loan by'),
                TextColumn::make('loanAmorts_pending_sum')
                    ->label('Pending Loan')
                    ->numeric()
                    ->prefix('NGN')
                    ->getStateUsing(function ($record) {
                        return $record->loan->loanAmorts()
                            ->where('status', '!=', 'paid')
                            ->where('status', '!=', 'complete')
                            ->sum('principal');
                    }),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected'
                    ]),
                SelectFilter::make('member_id')
                    //->relationship('applicant', 'name')
                    ->options([Auth::user()->member->slug => Auth::user()->member->name])
                    ->label('Applicant')
                    ->selectablePlaceholder(false)
                    ->default(Auth::user()->member->slug),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalWidth(Width::Medium)
                    ->slideOver(),
                Action::make('authorize')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);

                        $user = $record->loan->member->user;
                        Notification::make()
                            ->title('Guarantor Request Approved')
                            ->body("{$record->member->name} Approved your guarantor request  Successfully")
                            ->actions([
                                Action::make('view')
                                    ->button()
                                    ->markAsRead()
                                    ->url("loans")
                            ])
                            ->sendToDatabase($user);

                        Notification::make()
                            ->title('Guarantee Approved')
                            ->success()
                            ->body('Guarantee Approved Successfully')
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGuarantors::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('loan.loanAmorts', function ($query) {
                $query->where('status', '!=', 'paid')
                    ->where('status', '!=', 'complete');
            });
    }
}
