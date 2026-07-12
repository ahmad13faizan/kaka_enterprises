<x-admin::layouts>
    <x-slot:title>
        Site Visit #{{ $visit->id }}
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.site-visits.index') }}" class="transparent-button">
                <span class="icon-arrow-left text-2xl"></span>
            </a>
            <p class="text-xl font-bold !leading-normal text-gray-800 dark:text-white">
                Site Visit #{{ $visit->id }}
            </p>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                @if($visit->status === 'completed') bg-green-100 text-green-800
                @elseif($visit->status === 'cancelled') bg-red-100 text-red-800
                @elseif($visit->status === 'scheduled') bg-blue-100 text-blue-800
                @elseif($visit->status === 'assigned') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($visit->status) }}
            </span>
        </div>
    </div>

    <div class="flex gap-2.5 mt-3.5 max-xl:flex-wrap">
        {{-- Left column: Details --}}
        <div class="flex flex-col gap-2 flex-1 max-xl:flex-auto">

            {{-- Visit Details --}}
            <div class="bg-white dark:bg-gray-900 rounded box-shadow p-4">
                <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">Visit Details</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Requester</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ $visit->customer->first_name ?? '' }} {{ $visit->customer->last_name ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Product</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ $visit->product->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Address</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $visit->address }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Preferred Date</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ $visit->preferred_date->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Preferred Time</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ ucfirst($visit->preferred_time_slot) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Assigned Rep</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ $visit->assignedRep->name ?? 'Unassigned' }}
                        </p>
                    </div>
                    @if($visit->notes)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-600 dark:text-gray-300">Notes</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $visit->notes }}</p>
                    </div>
                    @endif
                    @if($visit->scheduled_date)
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Scheduled</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ $visit->scheduled_date->format('d M Y') }} — {{ $visit->scheduled_time }}
                        </p>
                    </div>
                    @endif
                    @if($visit->completed_at)
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Completed</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $visit->completed_at->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                    @if($visit->completion_notes)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-600 dark:text-gray-300">Completion Notes</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $visit->completion_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Status History --}}
            <div class="bg-white dark:bg-gray-900 rounded box-shadow p-4">
                <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">Status History</p>

                @if($visit->statusLogs->isEmpty())
                    <p class="text-sm text-gray-500">No status changes recorded yet.</p>
                @else
                    <div class="grid gap-3">
                        @foreach($visit->statusLogs as $log)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">
                                        {{ ucfirst($log->previous_status) }} → {{ ucfirst($log->new_status) }}
                                    </p>
                                    @if($log->notes)
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $log->notes }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">{{ $log->changedBy->name ?? 'System' }}</p>
                                    <p class="text-xs text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Right column: Actions --}}
        <div class="flex flex-col gap-2 w-[360px] max-w-full max-xl:w-full">

            {{-- Assign Rep --}}
            @if(in_array('assigned', $allowedTransitions) || $visit->status === 'assigned')
            <div class="bg-white dark:bg-gray-900 rounded box-shadow p-4">
                <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">Assign Sales Rep</p>
                <form method="POST" action="{{ route('admin.site-visits.assign', $visit->id) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="assigned_rep_id" class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white" required>
                            <option value="">-- Select Rep --</option>
                            @foreach($salesReps as $rep)
                                <option value="{{ $rep->id }}" {{ $visit->assigned_rep_id == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="primary-button w-full">Assign</button>
                </form>
            </div>
            @endif

            {{-- Update Status --}}
            @if(!empty($allowedTransitions))
            <div class="bg-white dark:bg-gray-900 rounded box-shadow p-4">
                <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">Update Status</p>
                <form method="POST" action="{{ route('admin.site-visits.update-status', $visit->id) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="status" id="sv-status" class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white" required>
                            @foreach($allowedTransitions as $transition)
                                @if($transition !== 'assigned')
                                    <option value="{{ $transition }}">{{ ucfirst($transition) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div id="sv-schedule-fields" class="mb-3 hidden">
                        <input type="date" name="scheduled_date" class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white mb-2" placeholder="Date">
                        <input type="text" name="scheduled_time" class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white" placeholder="Time (e.g. 10:00 AM)">
                    </div>

                    <div class="mb-3">
                        <textarea name="notes" rows="2" class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white" placeholder="Notes (optional)"></textarea>
                    </div>

                    <button type="submit" class="primary-button w-full">Update Status</button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('sv-status')?.addEventListener('change', function() {
            const fields = document.getElementById('sv-schedule-fields');
            fields.classList.toggle('hidden', this.value !== 'scheduled');
        });
    </script>
</x-admin::layouts>
