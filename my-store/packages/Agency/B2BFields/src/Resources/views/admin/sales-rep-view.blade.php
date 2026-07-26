{{-- Sales Representative display on the admin customer view page --}}
@php
    $reps = \Webkul\User\Models\Admin::pluck('name', 'id');
    $repMapJson = $reps->toJson();
@endphp

<template v-if="customer && customer.sales_rep_id">
    <div class="mb-2.5 flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-base font-semibold text-gray-800 dark:text-white">
            Sales Representative
        </p>

        <p class="break-all text-gray-600 dark:text-gray-300"
           v-text="({{ $repMapJson }})[customer.sales_rep_id] ?? ('#' + customer.sales_rep_id)">
        </p>
    </div>
</template>
