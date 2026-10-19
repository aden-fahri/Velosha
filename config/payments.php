<?php

return [
    'simulation' => filter_var(env('PAYMENT_SIMULATION', false), FILTER_VALIDATE_BOOLEAN)
        && in_array(env('APP_ENV', 'production'), ['local', 'testing'], true),
];