<section class="w-full">
    @include('partials.settings-heading')
    <x-settings.layout :heading="__('Update membership data')" :subheading="__('Details below will be used to process your membership with the society.')">
    <form wire:submit="save" class="my-6 w-full space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-4">
            <div class="flex items-center justify-between">
                <flux:button variant="primary" type="submit" class="w-full">{{ __('Save changes') }}</flux:button>
            </div>
        </div>
    </form>

    <x-filament-actions::modals />
    </x-settings.layout>
</section>
