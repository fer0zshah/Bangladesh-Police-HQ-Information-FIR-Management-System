@extends('layouts.app')

@section('content')
    <h2>Registered Police Officers</h2>

    <table>
        <thead>
            <tr>
                <th>Badge Number</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Assigned Station</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($officers as $officer)
                <tr>
                    <td><strong>{{ $officer->badge_number }}</strong></td>
                    <td>{{ $officer->name }}</td>
                    <td>{{ $officer->rank }}</td>
                    <td>{{ $officer->station ? $officer->station->name : 'Unassigned' }}</td>
                    <td>{{ $officer->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection