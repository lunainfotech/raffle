@extends('layouts.frontend')

@section('title', 'Registration Closed')

@section('content')
<div class="max-w-xl mx-auto px-4 py-16 text-center">
    <h1 class="text-3xl font-bold text-red-700 mb-6">Registration Closed</h1>
    <p class="text-lg text-gray-800">
        {{ $message ?? 'Registrations are currently closed. Please check back later.' }}
    </p>
    <a href="{{ url('/') }}" class="inline-block mt-8 px-6 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">Go Home</a>
</div>
@endsection