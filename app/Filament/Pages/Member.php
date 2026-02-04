<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Users\UserResource;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Member extends Page  implements HasForms
{
    use HasPageShield;
    protected static ?int $navigationSort = 1;
    protected static string | UnitEnum | null $navigationGroup = 'Savings';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedIdentification;
    protected string $view = 'filament.pages.member';
    use InteractsWithForms;

    protected static string $resource = UserResource::class;
    protected static bool $shouldRegisterNavigation = true;
    public ?array $data = []; // Holds form data
    public function mount(): void
    {
        $member = auth()->user()->member;

        if ($member) {
            $this->form->fill($member->toArray());
        } else {
            // Optional: handle cases where the user isn't a member yet
            $this->form->fill();
        }
    }
    protected ?string $heading = 'Update saving';
    protected static ?string $navigationLabel = 'Saving update';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        Hidden::make('slug')
                            ->required(),
                        Hidden::make('user_id'),
                        TextInput::make('name')
                            ->required(),
                        Hidden::make('gender')
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
                        TextInput::make('status')
                            ->readOnly()
                            ->required(),
                    ])->columns(2)
            ])->model(\App\Models\Member::class)
            ->statePath('data');
    }

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.index';
    }

    public function save(): void
    {
        // 1. Get the validated data from the form
        $state = $this->form->getState();

        // 2. Get the specific member record for the logged-in user
        $member = auth()->user()->member();

        if ($member) {
            // 3. Update the record in the database
            $member->update($state);

            // 4. Send a success notification
            Notification::make()
                ->title('Profile Updated')
                ->body('The member savings and details have been saved.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Error')
                ->body('Member record not found.')
                ->danger()
                ->send();
        }
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full; // Options: ExtraSmall, Small, Medium, Large, etc.
    }
}
