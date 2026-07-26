<?php

return [
    'redirect-title'   => 'Redirecting to Razorpay',
    'redirect-message' => 'Please wait, opening secure payment window...',
    'pay-now'          => 'Pay Now',

    'error-no-cart'              => 'Your cart could not be found. Please try again.',
    'error-not-configured'       => 'Razorpay is not configured correctly. Please contact the store.',
    'error-gateway'              => 'Unable to reach the payment gateway. Please try again.',
    'error-payment-failed'       => 'Your payment could not be completed. Please try again.',
    'error-verification-failed'  => 'Payment verification failed. If money was deducted it will be refunded.',
    'error-payment-cancelled'    => 'Payment was cancelled. Your cart has been saved.',

    'admin' => [
        'system' => [
            'title'             => 'Razorpay',
            'info'              => 'Accept UPI, Credit/Debit Cards, Netbanking and Wallet payments via Razorpay.',
            'field-title'       => 'Title',
            'field-description' => 'Description',
            'key-id'            => 'Razorpay Key ID',
            'key-id-info'       => 'Your Razorpay API Key ID (starts with rzp_test_ for sandbox or rzp_live_ for production).',
            'key-secret'        => 'Razorpay Key Secret',
            'key-secret-info'   => 'Your Razorpay API Key Secret. Keep this confidential.',
            'status'            => 'Status',
            'sort-order'        => 'Sort Order',
        ],
    ],
];
