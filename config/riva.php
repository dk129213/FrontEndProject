<?php

return [

    /*
    | Path prefix the site is served under. Empty locally, "/<repo>" on
    | GitHub Pages. Every internal link and asset goes through it.
    */
    'base_path' => env('APP_BASE_PATH', ''),

    /*
    | Public address of the site, used for the QR code that opens the
    | login page on a phone. Defaults to the live GitHub Pages site, so the
    | QR code works for visitors even when shown from a local copy.
    */
    'public_url' => env('PUBLIC_URL') ?: 'https://dk129213.github.io/FrontEndProject',

    'dealer' => [
        'name' => 'Riva Motors',
        'city' => 'Dubrovnik',
        'street' => 'Gruž',
        'postcode' => '20000',
        'phone' => '+385 20 000 142',
        'email' => 'hello@rivamotors.example',
        'hours' => [
            ['Mon to Fri', '08:00 to 19:00'],
            ['Saturday', '09:00 to 14:00'],
            ['Sunday', 'Closed'],
        ],
    ],

    'vat_rate' => 25,

];
