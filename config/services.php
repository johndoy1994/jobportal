<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    // Laravel Socialite

    'facebook' => [
        // 'client_id'     => '1018541661533312',
        // 'client_secret' => 'b2ec51daf86d88aac577a89adf5a8b72',
        // 'redirect'      => env('APP_URL').'/account/through/facebook/callback',
        'client_id'     => '563254513857781',
        'client_secret' => '458f1f0503d3f0dfdd25ea90935f0335',
        'redirect'      => env('APP_URL').'/account/through/facebook/callback',
    ],

    'linkedin' => [
        'client_id'     => '75mya893aad6bo',
        'client_secret' => 'HTZLHnXthZ9gwxKK',
        'redirect'      => env('APP_URL').'/account/through/linkedin/callback',
    ],

    'google' => [
        'client_id' => "254246869115-mhhc1ajkq4pmv8lfcfui2ckiden7b4rn.apps.googleusercontent.com",
        'client_secret' => "qYxqju6jbspStyxDSFKvvd9e",
        'redirect' => env('APP_URL') . '/account/through/google/callback',
    ],

     'twilio' => [
        'sid' => 'AC0edec1dfdb79c186d697678765ef8125',
        'token' => '9dd1292d5a3b6bb7581d057afa2ac568',
        'from' => '+16476943987',
        'ssl_verify' => false,
    ]    

    // 'twilio' => [
    //     'sid' => 'ACc55089a983978b4677fe51bbd31a1645',
    //     //'sid' => 'ACf69ec9cec2e7b43922300043d83e6a72', //test credential
    //     'token' => '34247bed41a91630a07afb01779fb19f',
    //     //'token' => '5be211b6409392a3b166d09d92ce8907', //test credential
    //     'from' => '+16476943987',
    //     //'from' => '+14243205944', //'+15005550006', // test credential
    //     'ssl_verify' => false, // Development switch to bypass API SSL certificate verfication
    // ]
];
