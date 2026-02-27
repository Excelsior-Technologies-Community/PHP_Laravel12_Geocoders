# PHP_Laravel12_Geocoder


## Project Description

PHP_Laravel12_Geocoder is a Laravel 12 based geocoding application that converts human-readable addresses into geographic coordinates such as latitude and longitude using the OpenStreetMap Nominatim API.

This project demonstrates how to integrate geocoding functionality in Laravel without using paid services like Google Maps. It uses the Geocoder PHP library with the Nominatim provider and Guzzle HTTP client to fetch accurate location data.

The application accepts an address as input and returns detailed location information including display name, coordinates, country, postal code, and full raw response in JSON format.

This project is useful for learning geocoding concepts, Laravel API integration, and working with external location services.


## Features

- Convert address into latitude and longitude

- Fetch complete location details

- Uses free OpenStreetMap API (no billing required)

- RESTful API response in JSON format

- Clean and structured Laravel project

- Beginner-friendly implementation

- No Google Maps API key required

- Fast and accurate geocoding results



## Technologies Used

- PHP 8.2+

- Laravel 12

- Composer

- MySQL

- Geocoder PHP Library

- Nominatim Provider (OpenStreetMap)

- Guzzle HTTP Client

- REST API

- JSON



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Geocoders "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Geocoders

```

#### Explanation:

Installs a fresh Laravel 12 project and navigates into the project folder.





## STEP 2: Database Setup 

### Generate key:

```
php artisan key:generate

```

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_geocoder
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_geocoder

```

#### Explanation:

Create the database laravel12_geocoder in MySQL / phpMyAdmin.

This connects your Laravel app to MySQL to store any future data.





## STEP 3: Install Geocoder Packages

### Install the package:

```
composer require geocoder-php/nominatim-provider guzzlehttp/guzzle php-http/message

```

#### Explanation:

Installs the Nominatim provider and Guzzle HTTP client for geocoding.






## STEP 4: Publish Config File

### This command publishes the geocoder config:

```
php artisan vendor:publish --provider="Geocoder\Laravel\Providers\GeocoderService"

```

### You’ll now have:

```
config/geocoder.php

```


#### Explanation:

This allows you to configure your geocoder settings like provider URL and locale.





## STEP 5: Configure Nominatim


### Then edit config/geocoder.php:

```
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


```

#### Explanation:

Configures Nominatim as the geocoding provider and sets a user agent (required by Nominatim API).




## STEP 6: Create Controller

### Make a controller:

```
php artisan make:controller GeocodersController

```

### In app/Http/Controllers/GeocodersController.php:

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Query\GeocodeQuery;
use Geocoder\StatefulGeocoder;
use GuzzleHttp\Client as GuzzleClient;

class GeocodersController extends Controller
{
    public function index(Request $request)
    {
        $address = $request->get('address', 'Eiffel Tower');

        // Initialize Guzzle client
        $guzzle = new GuzzleClient();

        // Initialize Nominatim provider
        $provider = Nominatim::withOpenStreetMapServer($guzzle, 'LaravelGeocoderApp');

        // Initialize Geocoder
        $geocoder = new StatefulGeocoder($provider, 'en');

        // Geocode address
        $results = $geocoder->geocodeQuery(GeocodeQuery::create($address));

        if ($results->isEmpty()) {
            return response()->json(['error' => 'No location found'], 404);
        }

        // Nominatim returns NominatimAddress objects
        $loc = $results->first();

        return response()->json([
            'display_name' => $loc->getDisplayName(), // correct method
            'latitude' => $loc->getCoordinates()->getLatitude(),
            'longitude' => $loc->getCoordinates()->getLongitude(),
            'raw' => $loc->toArray() // optional: full raw data
        ]);
    }
}

```

#### Explanation:

Controller fetches address from query string, geocodes it, and returns JSON with full details.




## STEP 7: Define Routes

### Open: routes/web.php

#### Add:

```
use App\Http\Controllers\GeocodersController;

Route::get('/geocode', [GeocodersController::class, 'index']);

```

#### Explanation:

Defines a GET route /geocode to call the geocoder controller.





## STEP 8: Test

### Run:

```
php artisan serve

```

### Then in browser:

```
http://127.0.0.1:8000/geocode?address=Statue+of+Liberty

```

### Expected output:


<img width="1919" height="938" alt="Screenshot 2026-02-27 104245" src="https://github.com/user-attachments/assets/92b2d8b7-2d00-4066-bc73-4f3c55eab8aa" />


---

# Project Folder Structure:

```
PHP_Laravel12_Geocoders/
├─ app/
│  ├─ Http/
│  │  ├─ Controllers/
│  │  │  └─ GeocodersController.php
│  └─ Models/
├─ config/
│  └─ geocoder.php
├─ database/
├─ public/
├─ resources/
├─ routes/
│  └─ web.php
├─ storage/
├─ tests/
├─ .env
├─ composer.json
├─ artisan
└─ ...

```

