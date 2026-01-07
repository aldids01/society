<?php

namespace App\Livewire;

use App\Models\Member;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class MemberUpdate extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Member $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->record = Member::query()->where('slug', auth()->user()->member->slug)->firstOrFail();
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make(fn() => $this->record->name .' Details')
                    ->schema([
                        TextInput::make('slug')
                            ->required(),
                        Select::make('user_id')
                            ->relationship('user', 'name'),
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
                    ])->columnSpanFull()
            ])->columns(1)
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->iconColor('success')
            ->body('Membership details have been saved successfully.')
            ->send();
    }


    public function render(): View
    {
        return view('livewire.member-update');
    }
}
