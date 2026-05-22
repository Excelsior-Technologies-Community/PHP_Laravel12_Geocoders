<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Simple Geocoder - Address Search</title>
    
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 2px solid #4a90e2;
            display: inline-block;
        }

        /* Form */
        .search-form {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .search-form input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-form input:focus {
            outline: none;
            border-color: #4a90e2;
        }

        button, .btn {
            padding: 10px 20px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        button:hover, .btn:hover {
            background: #357abd;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-success {
            background: #27ae60;
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Result */
        .result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .result p {
            margin: 8px 0;
            font-size: 14px;
        }

        .result strong {
            color: #555;
            display: inline-block;
            width: 80px;
        }

        .result a {
            color: #4a90e2;
            text-decoration: none;
        }

        .result a:hover {
            text-decoration: underline;
        }

        /* Map */
        #map {
            height: 250px;
            border-radius: 6px;
            margin-top: 15px;
        }

        /* History List */
        .history-list {
            margin-top: 15px;
            max-height: 400px;
            overflow-y: auto;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .history-item:hover {
            background: #f9f9f9;
        }

        .history-info {
            flex: 1;
            cursor: pointer;
        }

        .history-address {
            font-weight: 500;
            color: #333;
        }

        .history-coords {
            font-size: 11px;
            color: #888;
            margin-top: 3px;
        }

        .history-actions {
            display: flex;
            gap: 8px;
        }

        .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            transition: opacity 0.2s;
        }

        .icon-btn:hover {
            opacity: 0.7;
        }

        .icon-btn.view {
            color: #4a90e2;
        }

        .icon-btn.delete {
            color: #e74c3c;
        }

        /* Reverse Geocode */
        .reverse-box {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .coord-inputs {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }

        .coord-inputs input {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }

        /* Alert */
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* History header */
        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
            background: #e9ecef;
            color: #495057;
            margin-left: 8px;
        }

        .badge-forward {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-reverse {
            background: #fff3cd;
            color: #856404;
        }

        .empty-state {
            text-align: center;
            color: #999;
            padding: 30px;
            font-size: 14px;
        }

        hr {
            margin: 15px 0;
            border: none;
            border-top: 1px solid #eee;
        }

        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Main Search Card -->
        <div class="card">
            <h2> Geocode Address</h2>
            
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <form method="GET" action="/geocode" class="search-form">
                <input type="text" name="address" placeholder="Enter address (e.g., Eiffel Tower, Paris)" value="{{ $search_address ?? '' }}">
                <button type="submit">Search</button>
            </form>
            
            @if(isset($display_name))
                <div class="result">
                    <p><strong> Address:</strong> {{ $display_name }}</p>
                    <p><strong> Lat:</strong> {{ $latitude }} | <strong>Lng:</strong> {{ $longitude }}</p>
                    <p><strong> Maps:</strong> <a href="{{ $google_maps_url }}" target="_blank">Open in Google Maps →</a></p>
                </div>
                <div id="map"></div>
            @endif
            
            <!-- Reverse Geocoding -->
            <div class="reverse-box">
                <strong> Reverse Geocode</strong>
                <div class="coord-inputs">
                    <input type="text" id="reverse-lat" placeholder="Latitude (e.g., 48.8584)">
                    <input type="text" id="reverse-lng" placeholder="Longitude (e.g., 2.2945)">
                </div>
                <button id="reverse-geocode-btn" style="width: 100%;">Get Address from Coordinates</button>
                <div id="reverse-result" style="margin-top: 10px;"></div>
            </div>
        </div>
        
        <!-- History Card -->
        <div class="card">
            <div class="history-header">
                <h2> Recent Searches</h2>
                <div style="display: flex; gap: 10px;">
                    <button id="export-csv-btn" class="btn-small btn-success"> Export CSV</button>
                    <button id="clear-history-btn" class="btn-small btn-danger"> Clear All</button>
                </div>
            </div>
            
            @if($history->count() > 0)
                <div class="history-list">
                    @foreach($history as $item)
                    <div class="history-item">
                        <div class="history-info" onclick="viewOnMap({{ $item->latitude }}, {{ $item->longitude }}, '{{ addslashes($item->display_name ?? $item->address) }}')">
                            <div class="history-address">
                                {{ Str::limit($item->display_name ?? $item->address, 50) }}
                                <span class="badge badge-{{ $item->search_type ?? 'forward' }}">
                                    {{ ucfirst($item->search_type ?? 'Forward') }}
                                </span>
                            </div>
                            <div class="history-coords">
                                {{ number_format($item->latitude, 4) }}, {{ number_format($item->longitude, 4) }}
                                <span style="color: #bbb;"> • {{ $item->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="history-actions">
                            <button class="icon-btn delete" onclick="event.stopPropagation(); deleteHistory({{ $item->id }})">🗑️</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    No search history yet. Try searching for an address!
                </div>
            @endif
        </div>
    </div>
    
    <!-- Loading Indicator -->
    <div id="loading" class="loading">
        Loading...
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map = null;
        let marker = null;
        
        @if(isset($latitude) && isset($longitude))
            initializeMap({{ $latitude }}, {{ $longitude }}, '{{ addslashes($display_name) }}');
        @endif
        
        function initializeMap(lat, lng, title) {
            if (map === null) {
                map = L.map('map').setView([lat, lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);
            } else {
                map.setView([lat, lng], 13);
                if (marker) map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(title).openPopup();
        }
        
        function viewOnMap(lat, lng, title) {
            initializeMap(lat, lng, title);
            document.getElementById('map')?.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Show/Hide Loading
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }
        
        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }
        
        // Reverse Geocoding
        document.getElementById('reverse-geocode-btn').addEventListener('click', async function() {
            const lat = document.getElementById('reverse-lat').value;
            const lng = document.getElementById('reverse-lng').value;
            const resultDiv = document.getElementById('reverse-result');
            
            if (!lat || !lng) {
                resultDiv.innerHTML = '<div class="alert alert-error">Please enter both latitude and longitude</div>';
                return;
            }
            
            showLoading();
            
            try {
                const response = await fetch('/reverse-geocode', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        latitude: parseFloat(lat), 
                        longitude: parseFloat(lng) 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success"> Address found: ${data.display_name}</div>`;
                    setTimeout(() => location.reload(), 1500);
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-error"> ${data.error}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="alert alert-error">Error processing request. Please try again.</div>';
            } finally {
                hideLoading();
            }
        });
        
        // Delete single history
        window.deleteHistory = async function(id) {
            if (!confirm('Delete this search?')) return;
            showLoading();
            
            try {
                const response = await fetch(`/history/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.error || 'Error deleting history');
                }
            } catch (error) {
                alert('Error deleting');
            } finally {
                hideLoading();
            }
        };
        
        // Clear all history
        document.getElementById('clear-history-btn').addEventListener('click', async function() {
            if (!confirm(' WARNING: This will delete ALL search history. Are you sure?')) {
                return;
            }
            
            showLoading();
            
            try {
                const response = await fetch('/history-clear', {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.error || 'Error clearing history');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error clearing history. Please try again.');
            } finally {
                hideLoading();
            }
        });
        
        // Export CSV
        document.getElementById('export-csv-btn').addEventListener('click', function() {
            window.location.href = '/export-csv';
        });
    </script>
</body>
</html>