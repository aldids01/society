<?php

namespace App\Livewire;

use App\Models\Member;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class NewMember extends Component implements HasActions, HasSchemas
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
        return $schema
            ->components([
                Section::make('Member Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->default(fn()=> Str::random(8))
                            ->required(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->unique('members', 'email')
                                    ->required(),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required(),
                            ])->createOptionModalHeading('Member access credentials'),
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
                        TextInput::make('saving')
                            ->required()
                            ->minValue(1000)
                            ->numeric()
                            ->default(1000),
                        Select::make('status')
                            ->options(['active' => 'Active', 'inactive' => 'Inactive', 'withdrawn' => 'Withdrawn'])
                            ->default('active')
                            ->required(),
                    ])->columnSpan(2),
                Section::make('Next of Kin Information')
                    ->schema([
                        TextInput::make('kin_name')
                            ->required(),
                        TextInput::make('kin_relationship')
                            ->required(),
                        TextInput::make('kin_phone')
                            ->tel(),
                        TextInput::make('kin_address'),
                    ])->columns(1),

            ])->columns(3)
            ->statePath('data')
            ->model(Member::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = Member::create($data);

        $this->form->model($record)->saveRelationships();

        redirect()->route('members.report');

        Notification::make()
            ->title('New member created')
            ->success()
            ->body('A new member has been created.')
            ->send();


    }

    #[Layout('components.layouts.admin')]
    #[title('New Member')]
    public function render(): View
    {
        return view('livewire.new-member');
    }
}
