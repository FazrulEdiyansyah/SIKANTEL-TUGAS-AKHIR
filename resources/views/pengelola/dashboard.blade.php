@extends('layouts.app')

@section('title', 'Pengelola Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Pengelola Dashboard
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Welcome, {{ Auth::user()->name }}
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 text-center">
            <p class="mb-4 text-gray-700">You are logged in as Pengelola.</p>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-telkom-red hover:bg-telkom-maroon focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-telkom-red">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
