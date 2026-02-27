<?php

use Geocoder\Provider\Nominatim\Nominatim;
use Http\Adapter\Curl\Client as CurlAdapter;

return [

    'cache-duration' => 9999999,

    'providers' => [
        Nominatim::class => [
            'root_url' => 'https://nominatim.openstreetmap.org/',
            'user_agent' => 'LaravelGeocoderTest', // required by Nominatim
            'locale' => 'en',
        ],
    ],

    'adapter' => CurlAdapter::class,
];