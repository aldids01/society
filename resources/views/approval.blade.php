<div>
    @foreach($records as $loan)
        <x-filament::fieldset :label="__($loan->member->name)">
            <x-filament::section>
                <x-slot name="heading">
                    Amt: {{ \Illuminate\Support\Number::format($loan->amount, 2) }}
                </x-slot>
                <x-slot name="description">
                    <span class="font-bold">Rate:</span> {{ $loan->rate }}%
                    <span class="mx-2">|</span>
                    <span class="font-bold">Terms:</span> {{ number_format($loan->terms) }} Month(s)
                </x-slot>

            </x-filament::section>
        </x-filament::fieldset>
    @endforeach
</div>
