<?php

namespace App\Livewire;

use App\Models\Member;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Hidden;
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

class SavingUpdate extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;


    public $record;

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
                Section::make('Monthly Saving Modification')
                    ->description('The new amount you shall update shall take effect from this month or next month continuously till you change it again.')
                    ->schema([
                        Fieldset::make('Amount')
                        ->schema([
                            TextInput::make('saving')
                                ->hiddenLabel()
                                ->required()
                                ->numeric()
                                ->columnSpanFull()
                                ->minValue(1000)
                                ->belowContent('Minimum amount of saving of each month is one thousand naira only (NGN 1,000)'),
                        ])
                    ])
            ])
            ->columns(2)
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
            ->body('Saving update have been saved successfully.')
            ->send();
    }


    #[title('Saving Modification')]
    public function render(): View
    {
        return view('livewire.saving-update');
    }
}
