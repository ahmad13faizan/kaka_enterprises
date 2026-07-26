{{-- GST number display on the admin customer view page (B2B / dealer accounts) --}}
<template v-if="customer && customer.gst_number">
    <div class="mb-2.5 flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-base font-semibold text-gray-800 dark:text-white">
            GST Number
        </p>

        <p class="break-all text-gray-600 dark:text-gray-300" v-text="customer.gst_number"></p>
    </div>
</template>
