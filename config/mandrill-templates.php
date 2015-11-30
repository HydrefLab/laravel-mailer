<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mandrill templates
    |--------------------------------------------------------------------------
    |
    | Set Mandrill templates you want to automatically create/update via Laravel command.
    |
    | Each template is written in form of array where:
    | - key is the name of the template (in Mandrill it will be converted to slug),
    | - value is the array with 7 fields (in most cases, only 'subject' and 'code' will be needing content different
    |   than example below).
    |
    */

    'Example template' => [
        'from_email' => null,
        'from_name' => null,
        'subject' => 'Example subject',
        'code' => 'Example template content',
        'text' => null,
        'publish' => true,
        'labels' => array(),
    ],
];
