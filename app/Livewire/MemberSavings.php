<?php

namespace App\Livewire;

use App\Models\Member;
use App\Models\Saving;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class MemberSavings extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->record = Saving::query()->where('member_id', request()->route('member'))->firstOrFail();
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->relationship('member', 'name')
                    ->disabled()
                    ->dehydrated(true)
                    ->required(),
                TextInput::make('annual')
                    ->required()
                    ->default('2024'),
                TextInput::make('January')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('February')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('March')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('April')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('May')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('June')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('July')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('August')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('September')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('October')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('November')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('December')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'withdrawn' => 'Withdrawn'])
                    ->default('active')
                    ->required(),
            ])->columns(2)
            ->inlineLabel()
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $updated = Saving::updateOrCreate(
            ['member_id' => $data['member_id'], 'annual' => $data['annual']],
            $data);
        $this->record = $updated;
        redirect()->route('members.report');

        Notification::make()
            ->title('Saving Updated')
            ->success()
            ->send();
    }

    #[Layout('components.layouts.admin')]
    #[Title('Member Savings')]
    public function render(): View
    {
        return view('livewire.member-savings');
    }
}
