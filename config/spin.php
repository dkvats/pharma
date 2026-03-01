<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Grand Spin Test Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, the grand spin requirement is reduced from 12 months
    | to 2 months for easier testing. Set to false in production.
    |
    */
    'grand_spin_test_mode' => env('GRAND_SPIN_TEST_MODE', false),
    
    /*
    |--------------------------------------------------------------------------
    | Grand Spin Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the yearly grand spin feature.
    |
    */
    'grand_spin' => [
        'required_months' => env('GRAND_SPIN_REQUIRED_MONTHS', 12),
        'test_mode_months' => 2,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Fraud Detection Settings
    |--------------------------------------------------------------------------
    |
    | Settings for detecting and preventing spin fraud.
    |
    */
    'fraud_detection' => [
        'max_attempts_per_minute' => 5,
        'suspicious_threshold' => 10,
        'suspicious_time_window' => 5, // minutes
    ],
];
