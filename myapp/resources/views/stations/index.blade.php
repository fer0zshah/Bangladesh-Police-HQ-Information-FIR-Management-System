<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Stations</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 40px; }
        h2 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #2980b9; color: white; }
        tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>

    <h2>Stations</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Station Name</th>
                <th>District</th>
                <th>Address</th>
                <th>Contact Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stations as $station)
                <tr>
                    <td>{{ $station->station_id }}</td>
                    <td>{{ $station->name }}</td>
                    <td>{{ $station->district }}</td>
                    <td>{{ $station->address }}</td>
                    <td>{{ $station->contact_number }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>