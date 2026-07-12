<?php

return [
    'admin' => [
        'menu' => [
            'site-visits' => 'Site Visits',
        ],

        'acl' => [
            'site-visits'   => 'Site Visits',
            'view'          => 'View',
            'assign'        => 'Assign',
            'update-status' => 'Update Status',
        ],

        'system' => [
            'site-visits'       => 'Site Visits',
            'site-visits-info'  => 'Configure site visit module settings',
            'general'           => 'General',
            'general-info'      => 'General site visit settings',
            'enabled'           => 'Enable Site Visits',
            'service-radius'    => 'Service Radius (km)',
            'service-radius-info' => 'Free visit radius in kilometers. Leave empty to hide from storefront.',
        ],

        'site-visits' => [
            'title'             => 'Site Visits',
            'id'                => 'ID',
            'requester'         => 'Requester',
            'product'           => 'Product',
            'address'           => 'Address',
            'preferred-date'    => 'Preferred Date',
            'status'            => 'Status',
            'assigned-rep'      => 'Assigned Rep',
            'created-at'        => 'Created At',
            'assign-rep'        => 'Assign Sales Rep',
            'update-status'     => 'Update Status',
            'view-details'      => 'View Details',
            'status-history'    => 'Status History',
            'completion-notes'  => 'Completion Notes',
            'scheduled-date'    => 'Scheduled Date',
            'scheduled-time'    => 'Scheduled Time',
            'no-visits'         => 'No site visit requests found.',
        ],
    ],

    'shop' => [
        'site-visits' => [
            'title'               => 'My Site Visits',
            'request-visit'       => 'Request Site Visit',
            'address'             => 'Visit Address',
            'preferred-date'      => 'Preferred Date',
            'preferred-time-slot' => 'Preferred Time',
            'product'             => 'Product of Interest',
            'notes'               => 'Additional Notes (Optional)',
            'submit'              => 'Submit Request',
            'cancel'              => 'Cancel Request',
            'service-radius-note' => 'Free visits within :radius km radius.',
            'status'              => 'Status',
            'no-visits'           => 'You have no site visit requests.',
            'success'             => 'Site visit request submitted successfully!',
            'cancelled'           => 'Site visit request cancelled.',
            'cancel-error'        => 'Only pending requests can be cancelled.',
        ],
    ],
];
