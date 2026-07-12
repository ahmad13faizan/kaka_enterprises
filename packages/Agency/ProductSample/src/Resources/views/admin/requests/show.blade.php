<x-admin::layouts>
    <x-slot:title>
        Sample Request #{{ $sampleRequest->id }}
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.sample-requests.index') }}" class="transparent-button">
                <span class="icon-arrow-left text-2xl"></span>
            </a>
            <p class="text-xl font-bold !leading-normal text-gray-800 dark:text-white">
                Sample Request #{{ $sampleRequest->id }}
            </p>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                @if($sampleRequest->status === 'delivered') bg-green-100 text-green-800
                @elseif($sampleRequest->status === 'rejected') bg-red-100 text-red-800
                @elseif($sampleRequest->status === 'shipped') bg-blue-100 text-blue-800
                @elseif($sampleRequest->status === 'approved') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($sampleRequest->status) }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 p-3 mb-4 bg-green-50 border border-green-200 rounded text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-2.5 mt-3.5 max-xl:flex-wrap">
        {{-- Left: Details --}}
        <div class="flex flex-col gap-2 flex-1 max-xl:flex-auto">
            <div class="bg-white dark:bg-gray-900 rounded box-shadow p-4">
                <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">Request Details</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Requester</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ $sampleRequest->customer->first_name ?? '' }} {{ $sampleRequest->customer->last_name ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Product</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">
                            {{ $sampleRequest->product->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Quantity</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $sampleRequest->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Requested On</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $sampleRequest->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-600 dark:text-gray-300">Shipping Address</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $sampleRequest->shipping_address }}</p>
                    </div>
                    @if($sampleRequest->rejection_reason)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-600 dark:text-gray-300">Rejection Reason</p>
                        <p class="text-sm text-red-600 font-medium">{{ $sampleRequest->rejection_reason }}</p>
                    </div>
                    @endif
                    @if($sampleRequest->shipped_at)
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Shipped</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $sampleRequest->shipped_at->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($sampleRequest->tracking_info)
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Tracking</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $sampleRequest->tracking_info }}</p>
                    </div>
                    @endif
                    @if($sampleRequest->delivered_at)
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Delivered</p>
                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ $sampleRequest->delivered_at->format('d M Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="flex flex-col gap-2 w-[360px] max-w-full max-xl:w-full">

            {{-- Approve/Reject (pending only) --}}
            @if($sampleRequest->status === 'pending')
            <div class="bg-white dark:bg-gray-900 rounded box-shadow p-4">
                <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">Actions</p>
                <div class="flex gap-2 mb-3">
                    <form method="POST" action="{{ route('admin.sample-requests.approve', $sampleRequest->id) }}" class="flex-1">
                        @csrf
                        <button type="submit" class="primary-button w-full">Approve</button>
                    </form>
                </div>
                <form method="POST" action="{{ route('admin.sample-requests.reject', $sampleRequest->id) }}">
                    @csrf
                    <input type="text" name="rejection_reason" placeholder="Rejection reason (optional)"
                        class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white mb-2">
                    <button type="submit" class="secondary-button w-full !bg-red-50 !text-red-600 !border-red-200 hover:!bg-red-100">Reject</button>
                </form>
            </div>
            @endif

            {{-- Update Status (approved/shipped) --}}
            @if(in_array($sampleRequest->status, ['approved', 'shipped']))
            <div class="bg-white dark:bg-gray-900 rounded box-shadow p-4">
                <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">Update Status</p>
                <form method="POST" action="{{ route('admin.sample-requests.update-status', $sampleRequest->id) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="status" class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white" required>
                            @foreach($allowedTransitions as $transition)
                                <option value="{{ $transition }}">{{ ucfirst($transition) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($sampleRequest->status === 'approved')
                    <div class="mb-3">
                        <input type="text" name="tracking_info" placeholder="Tracking info (optional)"
                            class="w-full rounded border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm dark:bg-gray-800 dark:text-white">
                    </div>
                    @endif
                    <button type="submit" class="primary-button w-full">Update</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-admin::layouts>
