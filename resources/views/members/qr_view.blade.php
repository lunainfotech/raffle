@extends('layouts.frontend') {{-- Adjust if using a different layout --}}

@section('title', 'Your Ticket')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">🎟️ Your Ticket</h2>

    <div class="text-center mb-6">
        <img src="{{ asset('storage/raffle_cards/' . $member->membership_number . '.png') }}" alt="Raffle Ticket" class="mx-auto rounded shadow-lg max-w-full">
        <a href="{{ asset('storage/raffle_cards/' . $member->membership_number . '.png') }}" download class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold shadow">
            ⬇️ Download Ticket
        </a>
    </div>

    <div class="border-t pt-6">
        <h3 class="text-xl font-semibold mb-3 text-gray-700">Member Details</h3>
        <ul class="text-gray-800 space-y-1">
            <li><strong>Membership No:</strong> {{ $member->membership_number }}</li>
            <li><strong>Name:</strong> {{ $member->first_name }} {{ $member->last_name }}</li>
            <li><strong>Email:</strong> {{ $member->email }}</li>
            <li><strong>Phone:</strong> {{ $member->phone }}</li>
            <li><strong>Address:</strong> {{ $member->address }}, {{ $member->city }}, {{ $member->state }}</li>
            <li><strong>Referred By:</strong> {{ $member->referred_by ?? 'N/A' }}</li>
            <li><strong>Referred Chapter:</strong> {{ $member->referred_chapter_name ?? 'N/A' }}</li>
            <li><strong>Amount Paid:</strong> ${{ number_format($member->amount) }}</li>
            <li><strong>Payment Status:</strong> {{ ucfirst($member->payment_status) }}</li>
        </ul>
    </div>
</div>
@endsection