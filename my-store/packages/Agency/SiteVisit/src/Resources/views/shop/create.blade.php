<x-shop::layouts.account>
    <x-slot:title>
        @lang('site-visit::app.shop.site-visits.request-visit')
    </x-slot>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center gap-3 max-sm:mb-5">
            <a href="{{ route('shop.site-visits.index') }}" class="text-gray-600 hover:text-gray-800">
                <span class="icon-arrow-left text-2xl"></span>
            </a>
            <h2 class="text-2xl font-medium max-sm:text-xl">
                @lang('site-visit::app.shop.site-visits.request-visit')
            </h2>
        </div>

        @if($serviceRadius)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-blue-800 text-sm">
                @lang('site-visit::app.shop.site-visits.service-radius-note', ['radius' => $serviceRadius])
            </div>
        @endif

        <form method="POST" action="{{ route('shop.site-visits.store') }}" class="max-w-lg">
            @csrf

            {{-- Product --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="product_id">
                    @lang('site-visit::app.shop.site-visits.product') *
                </label>
                <select name="product_id" id="product_id" class="w-full rounded border border-gray-300 px-3 py-2.5 text-sm" required>
                    <option value="">-- Select Product --</option>
                    @php $products = \Webkul\Product\Models\ProductFlat::where('locale', app()->getLocale())->where('status', 1)->get(); @endphp
                    @foreach($products as $product)
                        <option value="{{ $product->product_id }}" {{ old('product_id') == $product->product_id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Address --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="address">
                    @lang('site-visit::app.shop.site-visits.address') *
                </label>
                <textarea name="address" id="address" rows="3" class="w-full rounded border border-gray-300 px-3 py-2.5 text-sm" required>{{ old('address') }}</textarea>
                @error('address') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Preferred Date --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="preferred_date">
                    @lang('site-visit::app.shop.site-visits.preferred-date') *
                </label>
                <input type="date" name="preferred_date" id="preferred_date"
                       value="{{ old('preferred_date') }}"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2.5 text-sm" required>
                @error('preferred_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Time Slot --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="preferred_time_slot">
                    @lang('site-visit::app.shop.site-visits.preferred-time-slot') *
                </label>
                <select name="preferred_time_slot" id="preferred_time_slot" class="w-full rounded border border-gray-300 px-3 py-2.5 text-sm" required>
                    <option value="">-- Select Time --</option>
                    <option value="morning" {{ old('preferred_time_slot') === 'morning' ? 'selected' : '' }}>Morning</option>
                    <option value="afternoon" {{ old('preferred_time_slot') === 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                    <option value="evening" {{ old('preferred_time_slot') === 'evening' ? 'selected' : '' }}>Evening</option>
                </select>
                @error('preferred_time_slot') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="notes">
                    @lang('site-visit::app.shop.site-visits.notes')
                </label>
                <textarea name="notes" id="notes" rows="2" class="w-full rounded border border-gray-300 px-3 py-2.5 text-sm">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="primary-button px-8 py-3">
                @lang('site-visit::app.shop.site-visits.submit')
            </button>
        </form>
    </div>
</x-shop::layouts.account>
