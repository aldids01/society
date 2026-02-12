<?php

namespace App\Filament\Resources\Members;

use App\Filament\Resources\Members\Pages\ManageMembers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Models\GrainAmort;
use App\Models\LoanAmort;
use App\Models\Member;
use App\Models\Saving;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
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
use Filament\Forms\Components\Repeater\TableColumn;
use UnitEnum;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?int $navigationSort = 10;

    protected static string | UnitEnum | null $navigationGroup = 'Reports';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->createOptionForm(fn($schema) => UserForm::configure($schema))
                    ->createOptionModalHeading('Create new user'),
                TextInput::make('name')
                    ->required(),
                Select::make('gender')
                    ->options(['Male' => 'Male', 'Female' => 'Female'])
                    ->default('Male')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('kin_name')
                    ->required(),
                TextInput::make('kin_relationship')
                    ->required(),
                TextInput::make('kin_phone')
                    ->tel(),
                TextInput::make('kin_address'),
                TextInput::make('saving')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'withdrawn' => 'Withdrawn'])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('slug'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('gender')
                    ->badge(),
                TextEntry::make('phone'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('address'),
                TextEntry::make('kin_name'),
                TextEntry::make('kin_relationship'),
                TextEntry::make('kin_phone')
                    ->placeholder('-'),
                TextEntry::make('kin_address')
                    ->placeholder('-'),
                TextEntry::make('saving')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Member $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->paginated([10, 25, 50, 100, 'all'])
            ->deferLoading(true)
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
            ->filtersFormColumns(3)
            ->defaultSort('name', 'asc')
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
                    Action::make('activate')
                        ->requiresConfirmation()
                        ->icon(Heroicon::CheckBadge)
                        ->action(function (Member $member) {
                            $member->update(['status' => 'active']);

                            Notification::make()
                                ->title('Member activated')
                                ->success()
                                ->send();
                        })
                        ->visible(fn($record) => $record->status == 'inactive' || $record->status == 'withdrawn'),
                    Action::make('deactivate')
                        ->requiresConfirmation()
                        ->icon(Heroicon::OutlinedXCircle)
                        ->action(function (Member $member) {
                            $member->update(['status' => 'inactive']);

                            Notification::make()
                                ->title('Member deactivated')
                                ->success()
                                ->send();
                        })
                        ->visible(fn($record) => $record->status == 'active'),
                    Action::make('withdraw')
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
                    Action::make('viewSavings')
                        ->mountUsing(fn (Schema $form, $record) => $form->fill([
                            // This automatically maps the 'savings' relationship to the repeater
                            'savings' => $record->savings->toArray(),
                        ]))
                        ->slideOver()
                        ->modalHeading(fn ($record) => $record->name.' Total saving: ' . number_format($record->savings->sum('total'), 2))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->schema([
                            RepeatableEntry::make('savings')
                                ->grid(4)
                                ->schema([
                                    TextEntry::make('annual')->hiddenLabel(),
                                    ...array_map(fn ($month) =>
                                    TextEntry::make($month)->numeric(),
                                        ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'total']
                                    )
                                ])
                        ]),
                    Action::make('savingUpdate')
                        ->icon(Heroicon::BuildingLibrary)
                        ->mountUsing(fn (Schema $form, $record) => $form->fill([
                            // This automatically maps the 'savings' relationship to the repeater
                            'savings' => $record->savings->toArray(),
                        ]))
                        ->slideOver()
                        ->schema([
                            Repeater::make('savings')
                                ->relationship('savings')
                                ->collapsed()
                                ->grid(3)
                                ->schema([
                                    TextInput::make('annual')->numeric(),
                                    ...array_map(fn ($month) =>
                                    TextInput::make($month)->numeric(),
                                        ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                                    )
                                ])->itemLabel(fn (array $state): ?string => $state['annual'] ?? null)
                        ])
                        ->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Update Complete')
                                ->body('Monthly records have been reconciled.')
                                ->send();
                        })
                ])->button()->size(Size::ExtraSmall)
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
            'index' => ManageMembers::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
