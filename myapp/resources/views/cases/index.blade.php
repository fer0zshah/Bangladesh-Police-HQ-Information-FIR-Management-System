@extends('layouts.app')

@section('content')
    <h2>Registered Police Cases</h2>

    <table>
        <thead>
            <tr>
                <th>Case ID</th>
                <th>Title</th>
                <th>Station</th>
                <th>Investigating Officer</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cases as $case)
                <tr>
                    <td><strong>{{ $case->case_id }}</strong></td>
                    <td>{{ $case->case_title }}</td>
                    <td>{{ $case->station->name }}</td>
                    <td>{{ $case->officer->name }}</td>
                    <td>{{ $case->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection