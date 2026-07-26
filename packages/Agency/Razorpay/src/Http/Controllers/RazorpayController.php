<?php

namespace Agency\Razorpay\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

class RazorpayController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Create a Razorpay order and render the checkout page.
     */
    public function redirect()
    {
        $cart = Cart::getCart();

        if (! $cart) {
            session()->flash('error', trans('razorpay::app.error-no-cart'));

            return redirect()->route('shop.checkout.cart.index');
        }

        $keyId     = core()->getConfigData('sales.payment_methods.razorpay.key_id');
        $keySecret = core()->getConfigData('sales.payment_methods.razorpay.key_secret');

        if (empty($keyId) || empty($keySecret)) {
            session()->flash('error', trans('razorpay::app.error-not-configured'));

            return redirect()->route('shop.checkout.cart.index');
        }

        // Razorpay expects the amount in the smallest currency sub-unit (paise for INR).
        $amount = (int) round($cart->grand_total * 100);

        try {
            $api = new Api($keyId, $keySecret);

            $razorpayOrder = $api->order->create([
                'receipt'         => (string) $cart->id,
                'amount'          => $amount,
                'currency'        => $cart->cart_currency_code ?? 'INR',
                'payment_capture' => 1,
            ]);
        } catch (\Throwable $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());

            session()->flash('error', trans('razorpay::app.error-gateway'));

            return redirect()->route('shop.checkout.cart.index');
        }

        session()->put('razorpay_order_id', $razorpayOrder['id']);

        $data = [
            'key_id'          => $keyId,
            'razorpay_order'  => $razorpayOrder['id'],
            'amount'          => $amount,
            'currency'        => $cart->cart_currency_code ?? 'INR',
            'name'            => core()->getCurrentChannel()->name,
            'callback_url'    => route('razorpay.callback'),
            'cancel_url'      => route('razorpay.cancel'),
            'customer_name'   => trim(($cart->customer_first_name ?? '') . ' ' . ($cart->customer_last_name ?? '')),
            'customer_email'  => $cart->customer_email,
            'customer_phone'  => optional($cart->billing_address)->phone,
        ];

        return view('razorpay::redirect', $data);
    }

    /**
     * Verify the payment signature and place the Bagisto order.
     */
    public function callback()
    {
        $keyId     = core()->getConfigData('sales.payment_methods.razorpay.key_id');
        $keySecret = core()->getConfigData('sales.payment_methods.razorpay.key_secret');

        $paymentId = request()->input('razorpay_payment_id');
        $orderId   = request()->input('razorpay_order_id');
        $signature = request()->input('razorpay_signature');

        if (empty($paymentId) || empty($orderId) || empty($signature)) {
            session()->flash('error', trans('razorpay::app.error-payment-failed'));

            return redirect()->route('shop.checkout.cart.index');
        }

        try {
            $api = new Api($keyId, $keySecret);

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
            ]);
        } catch (\Throwable $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage());

            session()->flash('error', trans('razorpay::app.error-verification-failed'));

            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        if (! $cart) {
            session()->flash('error', trans('razorpay::app.error-no-cart'));

            return redirect()->route('shop.checkout.cart.index');
        }

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        Cart::deActivateCart();

        session()->forget('razorpay_order_id');
        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
     * Handle a cancelled / dismissed payment.
     */
    public function cancel()
    {
        session()->forget('razorpay_order_id');
        session()->flash('error', trans('razorpay::app.error-payment-cancelled'));

        return redirect()->route('shop.checkout.cart.index');
    }
}
