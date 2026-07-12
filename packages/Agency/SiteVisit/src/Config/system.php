<?php

return [
    [
        'key'  => 'site_visits',
        'name' => 'site-visit::app.admin.system.site-visits',
        'info' => 'site-visit::app.admin.system.site-visits-info',
        'sort' => 8,
    ],
    [
        'key'    => 'site_visits.general',
        'name'   => 'site-visit::app.admin.system.general',
        'info'   => 'site-visit::app.admin.system.general-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'enabled',
                'title'         => 'site-visit::app.admin.system.enabled',
                'type'          => 'boolean',
                'default'       => '1',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'service_radius',
                'title'         => 'site-visit::app.admin.system.service-radius',
                'type'          => 'text',
                'default'       => '',
                'channel_based' => true,
                'locale_based'  => false,
                'info'          => 'site-visit::app.admin.system.service-radius-info',
            ],
        ],
    ],
];
