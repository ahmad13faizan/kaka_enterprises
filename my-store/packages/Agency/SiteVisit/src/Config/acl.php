<?php

return [
    [
        'key'   => 'site-visits',
        'name'  => 'site-visit::app.admin.acl.site-visits',
        'route' => 'admin.site-visits.index',
        'sort'  => 8,
    ],
    [
        'key'   => 'site-visits.view',
        'name'  => 'site-visit::app.admin.acl.view',
        'route' => 'admin.site-visits.show',
        'sort'  => 1,
    ],
    [
        'key'   => 'site-visits.assign',
        'name'  => 'site-visit::app.admin.acl.assign',
        'route' => 'admin.site-visits.assign',
        'sort'  => 2,
    ],
    [
        'key'   => 'site-visits.update-status',
        'name'  => 'site-visit::app.admin.acl.update-status',
        'route' => 'admin.site-visits.update-status',
        'sort'  => 3,
    ],
];
