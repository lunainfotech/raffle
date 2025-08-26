@extends('layouts.frontend')

@section('title', 'Payment Failed')

@section('content')
<div class="container py-5">
    <h2 class="text-danger mb-4">❌ Payment Failed</h2>
    <div class="alert alert-danger">
        {{ $error }}
    </div>
    <a href="{{ route('members.create') }}" class="btn btn-secondary">Try Again</a>
</div>
@endsection
