<x-admin::layouts>
    <x-slot:title>
        @lang('product-sample::app.admin.sample-inventory.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <p class="text-xl font-bold !leading-normal text-gray-800 dark:text-white">
            @lang('product-sample::app.admin.sample-inventory.title')
        </p>
    </div>

    @php
        $editProductId = request()->get('edit');
        $editInventory = null;
        if ($editProductId) {
            $editInventory = \Agency\ProductSample\Models\SampleInventory::where('product_id', $editProductId)
                ->with('product')
                ->first();
        }
    @endphp

    @if($editInventory)
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900 mb-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
                Edit Sample Stock: {{ $editInventory->product?->name ?? 'Product #' . $editProductId }}
            </h3>
            
            <x-admin::form 
                method="POST" 
                :action="route('admin.sample-inventory.update', $editProductId)"
            >
                @csrf
                @method('PUT')

                <div class="flex gap-4 items-end">
                    <x-admin::form.control-group class="flex-1">
                        <x-admin::form.control-group.label class="required">
                            Sample Stock
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="sample_stock"
                            :value="old('sample_stock', $editInventory->sample_stock)"
                            rules="required|numeric|min:0"
                            label="Sample Stock"
                        />

                        <x-admin::form.control-group.error control-name="sample_stock" />
                    </x-admin::form.control-group>

                    <div class="flex gap-2 mb-2.5">
                        <button 
                            type="submit" 
                            class="primary-button"
                        >
                            Update Stock
                        </button>

                        <a 
                            href="{{ route('admin.sample-inventory.index') }}" 
                            class="transparent-button"
                        >
                            Cancel
                        </a>
                    </div>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    Currently allocated: {{ $editInventory->allocated }} | 
                    Available: {{ $editInventory->sample_stock - $editInventory->allocated }}
                </p>
            </x-admin::form>
        </div>
    @endif

    <x-admin::datagrid :src="route('admin.sample-inventory.index')" />
</x-admin::layouts>
