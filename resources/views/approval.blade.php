<div>
    <!-- Order your soul. Reduce your wants. - Augustine -->
    <div class="space-y-4">
        @foreach($records as $loan)
            <div class="p-4 border rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold text-sm">Rate {{ $loan->rate }}%</span>
                    <span class="font-bold text-sm">Terms {{ number_format($loan->terms) }} Month(s)</span>
                    <span class="text-xs font-medium px-2 py-1 bg-primary-100 text-primary-700 rounded-full">
                    NGN {{ number_format($loan->amount) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <div><strong>Applicant:</strong> {{ $loan->member->name }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
