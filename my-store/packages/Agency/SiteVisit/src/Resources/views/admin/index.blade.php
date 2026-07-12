<x-admin::layouts>
    <x-slot:title>
        @lang('site-visit::app.admin.site-visits.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <p class="text-xl font-bold !leading-normal text-gray-800 dark:text-white">
            @lang('site-visit::app.admin.site-visits.title')
        </p>
    </div>

    <x-admin::datagrid :src="route('admin.site-visits.index')" />
</x-admin::layouts>
