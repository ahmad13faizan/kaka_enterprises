<?php

return [
    [
        'key'    => 'sales.payment_methods.razorpay',
        'name'   => 'razorpay::app.admin.system.title',
        'info'   => 'razorpay::app.admin.system.info',
        'sort'   => 3,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'razorpay::app.admin.system.field-title',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'description',
                'title'         => 'razorpay::app.admin.system.field-description',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => true,
            ],
            [
                'name'          => 'key_id',
                'title'         => 'razorpay::app.admin.system.key-id',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'info'          => 'razorpay::app.admin.system.key-id-info',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'key_secret',
                'title'         => 'razorpay::app.admin.system.key-secret',
                'type'          => 'password',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'info'          => 'razorpay::app.admin.system.key-secret-info',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'active',
                'title'         => 'razorpay::app.admin.system.status',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'    => 'sort',
                'title'   => 'razorpay::app.admin.system.sort-order',
                'type'    => 'select',
                'options' => [
                    ['title' => '1', 'value' => 1],
                    ['title' => '2', 'value' => 2],
                    ['title' => '3', 'value' => 3],
                    ['title' => '4', 'value' => 4],
                    ['title' => '5', 'value' => 5],
                ],
            ],
        ],
    ],
];
