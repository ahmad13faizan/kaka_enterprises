<x-admin::layouts>
    <x-slot:title>
        @lang('product-sample::app.admin.sample-requests.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <p class="text-xl font-bold !leading-normal text-gray-800 dark:text-white">
            @lang('product-sample::app.admin.sample-requests.title')
        </p>
    </div>

    <x-admin::datagrid :src="route('admin.sample-requests.index')" />
</x-admin::layouts>
