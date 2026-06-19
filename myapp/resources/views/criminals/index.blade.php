@extends('layouts.app')

@section('content')
    <h2>Wanted Criminals</h2>

    <table>
        <thead>
            <tr>
                <th>Criminal ID</th>
                <th>NID</th>
                <th>Name</th>
                <th>Alias</th>
                <th>Date of Birth</th>
                <th>Wanted Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($criminals as $criminal)
                <tr>
                    <td><strong>{{ $criminal->criminal_id }}</strong></td>
                    <td>{{ $criminal->nid_number }}</td>
                    <td>{{ $criminal->name }}</td>
                    <td>{{ $criminal->alias }}</td>
                    <td>{{ $criminal->date_of_birth }}</td>
                    <td>
                        @if($criminal->wanted_status == 1)
                        <span style="color:red; font-weight:bold;">YES</span>
                        @else
                        <span style="color:green;">No</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection