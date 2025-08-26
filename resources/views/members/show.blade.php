@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Membership Details') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <p><strong>Name:</strong> {{ $member->first_name }} {{ $member->last_name }}</p>
                            <p><strong>Email:</strong> {{ $member->email }}</p>
                            <p><strong>Phone:</strong> {{ $member->phone }}</p>
                            <p><strong>Address:</strong> {{ $member->address }}</p>
                            <p><strong>City:</strong> {{ $member->city }}</p>
                            <p><strong>State:</strong> {{ $member->state }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Membership Information</h5>
                            <p><strong>Membership Number:</strong> {{ $member->membership_number }}</p>
                            <p><strong>Registration Date:</strong> {{ $member->created_at->format('F j, Y') }}</p>
                            <p><strong>Payment Status:</strong> {{ ucfirst($member->payment_status) }}</p>
                            <p><strong>Referred Chapter:</strong> {{ $member->referred_chapter_name }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Membership QR Code</h5>
                            <img src="data:image/png;base64,{{ $member->qr_code }}" alt="Membership QR Code" class="img-fluid">
                        </div>
                        <div class="col-md-6">
                            <h5>Payment Receipt</h5>
                            <p><strong>Payment ID:</strong> {{ $member->stripe_payment_id }}</p>
                            <p><strong>Amount Paid:</strong> $5,000.00</p>
                            <a href="{{ route('members.receipt', $member) }}" class="btn btn-primary" target="_blank">
                                Download Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 