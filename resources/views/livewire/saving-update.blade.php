<div>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex items-center gap-4 mt-5">
            <div class="flex items-center justify-between">
                <flux:button variant="primary" type="submit" class="w-full">{{ __('Save changes') }}</flux:button>
            </div>
        </div>

    </form>

    <x-filament-actions::modals />
</div>
