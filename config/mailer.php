<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    |
    | Mailer supports two engines for sending emails with use of
    | templating system: Mandrill and SendGrid.
    |
    | Supported: "mandrill", "sendgrid"
    |
    */

    'driver' => env('MAILER_DRIVER', 'mandrill'),

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    |
    | In order to send emails through Mandrill/SendGrid engines, application
    | must communicate with corresponding API. For this, api key needs to be
    | specified.
    |
    */

    'apikey' => env('MAILER_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('DEFAULT_MAIL_FROM', null),
        'name' => env('DEFAULT_MAIL_NAME', null),
    ],

];