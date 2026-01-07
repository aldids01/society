<div>
    <form wire:submit="save">
        {{ $this->form }}


        <div class="flex items-center justify-items-start mt-5 gap-4">
            <flux:button variant="primary" type="submit" class="">{{ __('Save changes') }}</flux:button>
            <flux:button variant="danger"  href="{{ route('members.report') }}">{{ __('Close') }}</flux:button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
