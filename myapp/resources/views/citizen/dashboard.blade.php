<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('citizens Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 font-semibold text-green-800">{{ session('success') }}</div>
                    @endif
                    Welcome, {{ auth()->user()->name }}! You are logged in as a citizen.
                    <br><br>
                    <a href="{{ route('citizen.complaints.create') }}" class="inline-flex rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">Submit a new complaint</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
