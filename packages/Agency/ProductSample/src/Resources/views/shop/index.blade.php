<x-shop::layouts.account>
    <x-slot:title>
        @lang('product-sample::app.shop.sample-requests.title')
    </x-slot>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center justify-between max-sm:mb-5">
            <h2 class="text-2xl font-medium max-sm:text-xl">
                @lang('product-sample::app.shop.sample-requests.title')
            </h2>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($requests->isEmpty())
            <div class="grid gap-3.5 place-content-center items-center py-10 text-center">
                <p class="text-xl text-gray-600">@lang('product-sample::app.shop.sample-requests.no-requests')</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-medium text-gray-600">@lang('product-sample::app.shop.sample-requests.product')</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">@lang('product-sample::app.shop.sample-requests.quantity')</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">@lang('product-sample::app.shop.sample-requests.status')</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3">{{ $request->product->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $request->quantity }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded
                                        @if($request->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                        @elseif($request->status === 'shipped') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('shop.sample-requests.cancel', $request->id) }}"
                                              onsubmit="return confirm('Cancel this request?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 text-xs hover:underline">
                                                @lang('product-sample::app.shop.sample-requests.cancel')
                                            </button>
                                        </form>
                                    @endif
                                    @if($request->status === 'shipped' && $request->tracking_info)
                                        <span class="text-xs text-gray-500">Tracking: {{ $request->tracking_info }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-shop::layouts.account>
