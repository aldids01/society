<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-20">
            <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">
                    Save Changes
                </span>

                <span wire:loading wire:target="save">
                    Saving...
                </span>
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
