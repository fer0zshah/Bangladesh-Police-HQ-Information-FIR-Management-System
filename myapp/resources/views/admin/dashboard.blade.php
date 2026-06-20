<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HQ Super Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Welcome, {{ auth()->user()->name }}! You are logged in as a Super Admin.
                    <br><br>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded">Create New Police Station</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>