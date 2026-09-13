<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin IP Filter Toggle
    |--------------------------------------------------------------------------
    |
    | When disabled (or when the allow-list below is empty) the filter fails
    | open and every admin request is allowed. This keeps the restriction
    | opt-in and reversible, so clearing the allow-list and reloading config
    | recovers from an accidental self-lockout without removing this package.
    |
    */

    'enabled' => env('ADMIN_IP_FILTER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Allowed IP List
    |--------------------------------------------------------------------------
    |
    | A comma-separated list of client IPs permitted to reach the admin panel.
    | Each entry may be a single IPv4/IPv6 address or an IPv4/IPv6 CIDR range,
    | e.g. "203.0.113.4,2401:4900::/32,10.0.0.0/8". Entries are trimmed, empty
    | values are dropped, and the list is capped at 100 entries. Malformed
    | entries are filtered later by the IpMatcher, so an invalid value never
    | breaks configuration loading.
    |
    */

    'allowlist' => array_slice(
        array_values(
            array_filter(
                array_map('trim', explode(',', (string) env('ADMIN_IP_ALLOWLIST', ''))),
                static fn (string $entry): bool => $entry !== ''
            )
        ),
        0,
        100
    ),
];
