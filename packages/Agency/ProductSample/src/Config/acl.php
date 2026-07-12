<?php

return [
    [
        'key'   => 'product-samples',
        'name'  => 'product-sample::app.admin.acl.samples',
        'route' => 'admin.sample-requests.index',
        'sort'  => 9,
    ],
    [
        'key'   => 'product-samples.requests',
        'name'  => 'product-sample::app.admin.acl.requests',
        'route' => 'admin.sample-requests.index',
        'sort'  => 1,
    ],
    [
        'key'   => 'product-samples.inventory',
        'name'  => 'product-sample::app.admin.acl.inventory',
        'route' => 'admin.sample-inventory.index',
        'sort'  => 2,
    ],
];
