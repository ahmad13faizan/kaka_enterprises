<x-admin::form.control-group>
    <x-admin::form.control-group.label>
        GST Number
    </x-admin::form.control-group.label>

    <x-admin::form.control-group.control
        type="text"
        id="gst_number"
        name="gst_number"
        rules="regex:^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"
        label="GST Number"
        placeholder="22AAAAA0000A1Z5"
    />

    <x-admin::form.control-group.error control-name="gst_number" />

    <p class="mt-1 text-xs text-gray-500">
        Optional. 15-character GSTIN for B2B / dealer accounts.
    </p>
</x-admin::form.control-group>
