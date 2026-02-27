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