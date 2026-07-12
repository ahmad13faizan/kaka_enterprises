<?php

return [
    [
        'key'  => 'product_samples',
        'name' => 'product-sample::app.admin.system.title',
        'info' => 'product-sample::app.admin.system.info',
        'sort' => 9,
    ],
    [
        'key'    => 'product_samples.general',
        'name'   => 'product-sample::app.admin.system.general',
        'info'   => 'product-sample::app.admin.system.general-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'enabled',
                'title'   => 'product-sample::app.admin.system.enabled',
                'type'    => 'boolean',
                'default' => '1',
            ],
            [
                'name'    => 'sample_limit',
                'title'   => 'product-sample::app.admin.system.sample-limit',
                'type'    => 'text',
                'default' => '2',
                'info'    => 'product-sample::app.admin.system.sample-limit-info',
            ],
        ],
    ],
];
