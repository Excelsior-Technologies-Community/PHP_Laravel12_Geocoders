<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Geocoder\StatefulGeocoder;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\SearchHistory;

class GeocodersController extends Controller
{
    // SHOW FORM PAGE WITH HISTORY
    public function form()
    {
        $history = SearchHistory::latest()->take(10)->get();
        return view('geocode', compact('history'));
    }

    // FORWARD GEOCODING
    public function index(Request $request)
    {
        $address = $request->get('address');
        
        if (!$address) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Address is required'], 400);
            }
            return redirect('/search')->with('error', 'Please enter an address');
        }

        try {
            $guzzle = new GuzzleClient();
            $provider = Nominatim::withOpenStreetMapServer($guzzle, 'LaravelGeocoderApp/1.0');
            $geocoder = new StatefulGeocoder($provider, 'en');
            $results = $geocoder->geocodeQuery(GeocodeQuery::create($address));

            if ($results->isEmpty()) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'No location found'], 404);
                }
                return redirect('/search')->with('error', 'No location found for: ' . $address);
            }

            $loc = $results->first();
            $latitude = $loc->getCoordinates()->getLatitude();
            $longitude = $loc->getCoordinates()->getLongitude();

            // Save search history
            SearchHistory::create([
                'address' => $address,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'display_name' => $loc->getDisplayName(),
                'search_type' => 'forward'
            ]);

            $history = SearchHistory::latest()->take(10)->get();
            
            // Remove getURI() - it doesn't exist in this version
            $result = [
                'display_name' => $loc->getDisplayName(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'google_maps_url' => "https://www.google.com/maps?q={$latitude},{$longitude}"
            ];

            if ($request->wantsJson()) {
                return response()->json($result);
            }

            return view('geocode', array_merge($result, ['history' => $history, 'search_address' => $address]));

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect('/search')->with('error', 'Geocoding service error: ' . $e->getMessage());
        }
    }

    // REVERSE GEOCODING
    public function reverse(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        try {
            $guzzle = new GuzzleClient();
            $provider = Nominatim::withOpenStreetMapServer($guzzle, 'LaravelGeocoderApp/1.0');
            $geocoder = new StatefulGeocoder($provider, 'en');
            
            $results = $geocoder->reverseQuery(
                ReverseQuery::fromCoordinates($request->latitude, $request->longitude)
            );

            if ($results->isEmpty()) {
                return response()->json(['error' => 'No address found for these coordinates'], 404);
            }

            $loc = $results->first();
            $address = $loc->getDisplayName();

            // Save to history
            SearchHistory::create([
                'address' => $address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'display_name' => $address,
                'search_type' => 'reverse'
            ]);

            return response()->json([
                'success' => true,
                'address' => $address,
                'display_name' => $loc->getDisplayName(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // GET HISTORY
    public function getHistory(Request $request)
    {
        $limit = $request->get('limit', 20);
        $history = SearchHistory::latest()->take($limit)->get();
        
        if ($request->wantsJson()) {
            return response()->json($history);
        }
        
        return view('history', compact('history'));
    }

    // DELETE SINGLE HISTORY
    public function deleteHistory($id)
    {
        try {
            $history = SearchHistory::findOrFail($id);
            $history->delete();
            
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'History deleted successfully']);
            }
            
            return redirect()->back()->with('success', 'History entry deleted');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'History not found'], 404);
            }
            return redirect()->back()->with('error', 'Failed to delete history');
        }
    }

    // CLEAR ALL HISTORY
    public function clearHistory()
    {
        SearchHistory::truncate();
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'All history cleared']);
        }
        
        return redirect('/search')->with('success', 'All search history cleared');
    }

    // EXPORT TO CSV
    public function exportCsv()
    {
        $histories = SearchHistory::latest()->get();
        
        $filename = 'geocoding_history_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w');
        
        // Add CSV headers
        fputcsv($handle, ['ID', 'Address', 'Display Name', 'Latitude', 'Longitude', 'Search Type', 'Searched At']);
        
        // Add data rows
        foreach ($histories as $history) {
            fputcsv($handle, [
                $history->id,
                $history->address,
                $history->display_name,
                $history->latitude,
                $history->longitude,
                $history->search_type,
                $history->created_at
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }
}