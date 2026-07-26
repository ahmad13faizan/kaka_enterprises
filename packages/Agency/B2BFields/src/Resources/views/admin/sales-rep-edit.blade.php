{{-- Sales Representative selector on the admin customer create/edit form --}}
@php
    $salesReps = \Webkul\User\Models\Admin::orderBy('name')->get(['id', 'name']);
@endphp

<x-admin::form.control-group>
    <x-admin::form.control-group.label>
        Sales Representative
    </x-admin::form.control-group.label>

    <x-admin::form.control-group.control
        type="select"
        id="sales_rep_id"
        name="sales_rep_id"
        ::value="customer.sales_rep_id"
        label="Sales Representative"
    >
        <option value="">Not assigned</option>

        @foreach ($salesReps as $rep)
            <option value="{{ $rep->id }}">{{ $rep->name }}</option>
        @endforeach
    </x-admin::form.control-group.control>

    <x-admin::form.control-group.error control-name="sales_rep_id" />

    <p class="mt-1 text-xs text-gray-500">
        Optional. The admin user who manages this customer / account.
    </p>
</x-admin::form.control-group>
