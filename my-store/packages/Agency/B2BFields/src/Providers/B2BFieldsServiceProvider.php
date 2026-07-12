<?php

namespace Agency\B2BFields\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Webkul\Customer\Models\Customer;

class B2BFieldsServiceProvider extends ServiceProvider
{
    /**
     * Indian GSTIN format: 2 digit state code + 10 char PAN + entity digit + 'Z' + checksum.
     * Example: 22AAAAA0000A1Z5
     */
    private const GST_REGEX = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'b2b-fields');

        $this->registerViewHooks();
        $this->registerSaveHooks();
    }

    /**
     * Inject the GST field into the admin customer create + edit forms.
     */
    protected function registerViewHooks(): void
    {
        // Create form (modal)
        Event::listen('bagisto.admin.customers.create.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('b2b-fields::admin.gst-field-create');
        });

        // Edit form
        Event::listen('bagisto.admin.customers.customers.view.edit.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('b2b-fields::admin.gst-field-edit');
        });
    }

    /**
     * Validate + persist gst_number when a customer is created/updated from admin.
     */
    protected function registerSaveHooks(): void
    {
        // Validate before create/update
        Event::listen('customer.create.before', fn () => $this->validateGst());
        Event::listen('customer.update.before', fn () => $this->validateGst());

        // Persist after create
        Event::listen('customer.create.after', function (Customer $customer) {
            $this->saveGst($customer);
        });

        // Persist after update
        Event::listen('customer.update.after', function (Customer $customer) {
            $this->saveGst($customer);
        });
    }

    /**
     * Validate the GST number format if one was provided.
     *
     * @throws ValidationException
     */
    protected function validateGst(): void
    {
        $gst = request('gst_number');

        if (empty($gst)) {
            return; // optional field
        }

        $validator = Validator::make(
            ['gst_number' => strtoupper($gst)],
            ['gst_number' => ['regex:' . self::GST_REGEX]],
            ['gst_number.regex' => 'The GST number format is invalid. Expected format: 22AAAAA0000A1Z5']
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Save gst_number onto the customer record.
     */
    protected function saveGst(Customer $customer): void
    {
        if (! request()->has('gst_number')) {
            return;
        }

        $gst = request('gst_number');

        $customer->gst_number = $gst ? strtoupper($gst) : null;
        $customer->save();
    }
}
