@extends('layouts.app')

@section('content')
    <h2>Registered Police Stations</h2>

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
@endsection