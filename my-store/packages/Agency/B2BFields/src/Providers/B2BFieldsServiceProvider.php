<?php

namespace Agency\B2BFields\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Webkul\Customer\Models\Customer;
use Agency\B2BFields\Support\IdProof;

class B2BFieldsServiceProvider extends ServiceProvider
{
    /**
     * Indian GSTIN format: 2 digit state code + 10 char PAN + entity digit + 'Z' + checksum.
     * Example: 22AAAAA0000A1Z5
     *
     * Sourced from the shared IdProof support class so the admin GST field and the
     * storefront id_proof (GST) validation share a single source of truth.
     */
    private const GST_REGEX = IdProof::GSTIN_PATTERN;

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'b2b-fields');

        $this->registerViewHooks();
        $this->registerSaveHooks();
        $this->registerGuestCheckoutEnforcement();
    }

    /**
     * Force guest checkout OFF whenever an admin logs in.
     *
     * Every order must belong to a registered, identifiable customer (needed for
     * clean GST / refund records). This resets the setting on each admin login so
     * it can never be silently left enabled.
     */
    protected function registerGuestCheckoutEnforcement(): void
    {
        Event::listen(Login::class, function (Login $event) {
            if ($event->guard !== 'admin') {
                return;
            }

            $code = 'sales.checkout.shopping_cart.allow_guest_checkout';

            $exists = DB::table('core_config')->where('code', $code)->exists();

            if ($exists) {
                DB::table('core_config')->where('code', $code)->update([
                    'value'      => '0',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('core_config')->insert([
                    'code'       => $code,
                    'value'      => '0',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    /**
     * Inject the GST field into the admin customer create + edit forms.
     */
    protected function registerViewHooks(): void
    {
        // Create form (modal)
        Event::listen('bagisto.admin.customers.create.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('b2b-fields::admin.gst-field-create');
            $viewRenderEventManager->addTemplate('b2b-fields::admin.sales-rep-edit');
        });

        // Edit form
        Event::listen('bagisto.admin.customers.customers.view.edit.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('b2b-fields::admin.gst-field-edit');
            $viewRenderEventManager->addTemplate('b2b-fields::admin.sales-rep-edit');
        });

        // View (detail) page — read-only GST + sales rep display
        Event::listen('bagisto.admin.customers.customers.view.card.accordion.customer.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('b2b-fields::admin.gst-field-view');
            $viewRenderEventManager->addTemplate('b2b-fields::admin.sales-rep-view');
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
        if (request()->has('gst_number')) {
            $gst = request('gst_number');
            $customer->gst_number = $gst ? strtoupper($gst) : null;
            $customer->save();
        }

        $this->saveSalesRep($customer);
    }

    /**
     * Save the assigned sales representative (admin user) onto the customer.
     */
    protected function saveSalesRep(Customer $customer): void
    {
        if (! request()->has('sales_rep_id')) {
            return;
        }

        $repId = request('sales_rep_id');

        $customer->sales_rep_id = $repId ?: null;
        $customer->save();
    }
}
