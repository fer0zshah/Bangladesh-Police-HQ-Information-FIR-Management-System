<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('OC Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Welcome, {{ auth()->user()->name }}! You are logged in as a OC.
                    <br><br>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded">Manage officers and cases</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>