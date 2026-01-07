<x-layouts.admin :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            @livewire(App\Livewire\StatOverView::class)
        </div>
        <div class="grid grid-cols-2 gap-4">
            @livewire(App\Livewire\LoanApproval::class)
            @livewire(App\Livewire\GrainApproval::class)
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">

            @livewire(App\Livewire\SavingOverView::class)
        </div>
    </div>
</x-layouts.admin>
