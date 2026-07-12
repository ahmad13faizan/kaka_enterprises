<x-shop::layouts.account>
    <x-slot:title>
        @lang('site-visit::app.shop.site-visits.title')
    </x-slot>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center justify-between max-sm:mb-5">
            <h2 class="text-2xl font-medium max-sm:text-xl">
                @lang('site-visit::app.shop.site-visits.title')
            </h2>
            <a href="{{ route('shop.site-visits.create') }}" class="primary-button px-5 py-2.5">
                @lang('site-visit::app.shop.site-visits.request-visit')
            </a>
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

        @if($visits->isEmpty())
            <div class="grid gap-3.5 place-content-center items-center py-10 text-center">
                <p class="text-xl text-gray-600">@lang('site-visit::app.shop.site-visits.no-visits')</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left font-medium text-gray-600">@lang('site-visit::app.shop.site-visits.product')</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">@lang('site-visit::app.shop.site-visits.preferred-date')</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">@lang('site-visit::app.shop.site-visits.preferred-time-slot')</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">@lang('site-visit::app.shop.site-visits.status')</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visits as $visit)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3">{{ $visit->product->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $visit->preferred_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ ucfirst($visit->preferred_time_slot) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded
                                        @if($visit->status === 'completed') bg-green-100 text-green-800
                                        @elseif($visit->status === 'cancelled') bg-red-100 text-red-800
                                        @elseif($visit->status === 'scheduled') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($visit->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($visit->status === 'requested')
                                        <form method="POST" action="{{ route('shop.site-visits.cancel', $visit->id) }}"
                                              onsubmit="return confirm('Cancel this request?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 text-xs hover:underline">
                                                @lang('site-visit::app.shop.site-visits.cancel')
                                            </button>
                                        </form>
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
