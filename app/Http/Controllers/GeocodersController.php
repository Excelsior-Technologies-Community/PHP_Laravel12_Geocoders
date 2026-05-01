<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Query\GeocodeQuery;
use Geocoder\StatefulGeocoder;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\SearchHistory;

class GeocodersController extends Controller
{
    // SHOW FORM PAGE
    public function form()
    {
        return view('geocode');
    }

    // API + UI RESULT HANDLER
    public function index(Request $request)
    {
        $address = $request->get('address', 'Eiffel Tower');

        $guzzle = new GuzzleClient();

        $provider = Nominatim::withOpenStreetMapServer(
            $guzzle,
            'LaravelGeocoderApp'
        );

        $geocoder = new StatefulGeocoder($provider, 'en');

        $results = $geocoder->geocodeQuery(
            GeocodeQuery::create($address)
        );

        if ($results->isEmpty()) {
            return response()->json([
                'error' => 'No location found'
            ], 404);
        }

        $loc = $results->first();

        $latitude = $loc->getCoordinates()->getLatitude();
        $longitude = $loc->getCoordinates()->getLongitude();

        // Save search history
        SearchHistory::create([
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        // If request is AJAX/API → JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'display_name' => $loc->getDisplayName(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'google_maps_url' => "https://www.google.com/maps?q={$latitude},{$longitude}",
                'raw' => $loc->toArray()
            ]);
        }

        // Otherwise → Blade view response
        return view('geocode', [
            'display_name' => $loc->getDisplayName(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'google_maps_url' => "https://www.google.com/maps?q={$latitude},{$longitude}"
        ]);
    }
}