<!DOCTYPE html>
<html>
<head>
    <title>Laravel Geocoder</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 30px;
        }

        .box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
        }

        input {
            width: 80%;
            padding: 10px;
        }

        button {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="box">

    <h2>Geocoder Search</h2>

    <form method="GET" action="/geocode">
        <input type="text" name="address" placeholder="Enter address">
        <button type="submit">Search</button>
    </form>

    @if(isset($display_name))
        <hr>

        <h3>Result</h3>

        <p><b>Address:</b> {{ $display_name }}</p>
        <p><b>Latitude:</b> {{ $latitude }}</p>
        <p><b>Longitude:</b> {{ $longitude }}</p>

        <a href="{{ $google_maps_url }}" target="_blank">
            🌍 Open in Google Maps
        </a>
    @endif

</div>

</body>
</html>