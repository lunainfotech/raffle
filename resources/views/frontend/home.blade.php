@extends('layouts.frontend')

@section('title', 'Home')
@section('head')
<style>
body{
    background:#ffe8b3!important;
}
nav{
    display:none!important;
}
main{
    padding:0!important;
}
</style>
@endsection

@section('content')
<div class="container mx-auto mt-3">

    <div class="text-center mb-10">
        <a href="/register"><img width="1600" height="900"
            src="https://rammandir2024.org/wp-content/uploads/2025/07/vhpa-raffle-2025.jpg" 
            alt="VHPA Fundraising Raffle" 
            class="w-full mx-auto rounded-b-2xl shadow-2xl border-danger"
        ></a>
    </div>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden mb-8 card">
        <div class="p-8 text-center">
            <h1 class="text-4xl sm:text-5xl mb-4 fw-lighter" style="color: #9e0009;">
                Welcome to Shree Ram lalla Rath Raffle
            </h1>
            <p class="text-lg sm:text-xl text-gray-700 mb-6">
                Participate in our grand fundraising raffle! The lucky winner will be announced on
                <strong class="text-red-600">October 4th, 2025</strong>.
            </p>
            <p class="text-lg sm:text-xl text-gray-700 mb-6">
                First 250 tickets get a special bonus!
            </p>
            <div class="bg-red-100 border mt-3 border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-bold text-center">
                Please note: Payments via Zelle, PayPal, or Venmo are <span class="text-red-800">not accepted</span>.
            </div>
            <a href="{{ route('members.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold px-8 py-3 rounded shadow">
                Buy Ticket Now!
            </a>
        </div>
    </div>

</div>
@endsection
