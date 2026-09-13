<?php

return [
    'errors' => [
        /*
         * Generic 403 message. It is intentionally neutral and identical for
         * every denied request so it does not disclose whether the admin path
         * exists (Requirement 3.5).
         */
        'forbidden' => 'You are not authorized to access this resource.',
    ],
];
