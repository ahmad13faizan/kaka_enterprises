<?php

return [
    'razorpay' => [
        'code'        => 'razorpay',
        'title'       => 'Razorpay (UPI / Cards / Netbanking)',
        'description' => 'Pay securely using UPI, Credit/Debit Cards, Netbanking or Wallets via Razorpay.',
        'class'       => 'Agency\Razorpay\Payment\Razorpay',
        'sandbox'     => true,
        'active'      => false,
        'sort'        => 3,
    ],
];
