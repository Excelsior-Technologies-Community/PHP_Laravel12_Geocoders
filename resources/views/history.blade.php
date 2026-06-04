<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search History - Laravel Geocoder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background: #f8f9fa;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary { background: #667eea; color: white; }
        .btn-secondary { background: #27ae60; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1> Search History</h1>
                <div style="display: flex; gap: 10px;">
                    <a href="/export-csv" class="btn btn-secondary">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <a href="/search" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Search
                    </a>
                </div>
            </div>
            
            @if($history->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Address</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Type</th>
                            <th>Searched At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ Str::limit($item->display_name ?? $item->address, 50) }}</td>
                            <td>{{ $item->latitude }}</td>
                            <td>{{ $item->longitude }}</td>
                            <td>{{ ucfirst($item->search_type ?? 'Forward') }}</td>
                            <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No search history found.</p>
            @endif
        </div>
    </div>
</body>
</html>