<?php

namespace Agency\Razorpay\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Payment\Payment\Payment;

class Razorpay extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'razorpay';

    /**
     * Get redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('razorpay.redirect');
    }

    /**
     * Is available.
     *
     * Razorpay only handles stockable (physical/virtual) checkout carts.
     *
     * @return bool
     */
    public function isAvailable()
    {
        if (! $this->cart) {
            $this->setCart();
        }

        return $this->getConfigData('active') && $this->cart?->hasOnlyStockableItems();
    }

    /**
     * Get payment method image.
     *
     * @return string
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/cash-on-delivery.png', 'shop');
    }
}
