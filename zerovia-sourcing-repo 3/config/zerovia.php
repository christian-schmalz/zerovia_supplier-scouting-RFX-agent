<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ZEROvia Hub API
    |--------------------------------------------------------------------------
    | Internal ESG data API for supplier scores and VSME data
    */
    'hub' => [
        'base_url'   => env('ZEROVIA_HUB_BASE_URL', 'https://hub.zerovia.ch/api/v1'),
        'api_key'    => env('ZEROVIA_HUB_API_KEY'),
        'timeout'    => env('ZEROVIA_HUB_TIMEOUT', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sourcing Defaults
    |--------------------------------------------------------------------------
    */
    'sourcing' => [
        'default_radius_km'     => 150,
        'default_min_esg'       => 60,
        'default_top_n'         => 5,
        'max_results'           => 50,

        // Default scoring weights (must sum to 100)
        'scoring_weights' => [
            'price'         => 30,
            'esg'           => 25,
            'delivery'      => 20,
            'certifications'=> 15,
            'quality'       => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | RFQ Settings
    |--------------------------------------------------------------------------
    */
    'rfq' => [
        'validity_days'         => 90,
        'lead_time_days'        => 21,
        'decision_offset_days'  => 35,
        'question_deadline_days'=> 7,
        'submission_email'      => env('RFQ_SUBMISSION_EMAIL', 'procurement@zerovia.ch'),
        'from_name'             => 'ZEROvia Procurement',
        'company_name'          => 'ZEROvia GmbH',
        'company_uid'           => 'CHE-387.599.569',
        'company_address'       => '7153 Schluein, Graubünden, Schweiz',
        'company_url'           => 'zerovia.ch',
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance References
    |--------------------------------------------------------------------------
    */
    'compliance' => [
        'frameworks' => ['OR 964b-j', 'EU LkSG', 'CSRD/ESRS', 'VSME'],
        'mandatory_certs' => ['CE', 'REACH', 'RoHS'],
        'recommended_certs' => ['ISO 9001', 'ISO 14001', 'UN Global Compact'],
    ],

];
