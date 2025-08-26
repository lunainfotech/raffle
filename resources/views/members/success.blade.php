@extends('layouts.frontend')

@section('title', 'Registration Successful')

@section('content')
<div class="container py-5">
    <h2 class="text-success mb-4 text-center">✅ Payment Successful!</h2>

    <div class="card p-4 shadow mx-auto" style="max-width: 800px;">
        <p><strong>Name:</strong> {{ $members[0]->first_name }} {{ $members[0]->last_name }}</p>
        <p><strong>Email:</strong> {{ $members[0]->email }}</p>
        <p><strong>Total Tickets:</strong> {{ count($members) }}</p>
        <p><strong>Total Amount Paid:</strong> ${{ number_format( $totalCharge, 2) }}</p>
    </div>

    <div class="mt-5">
        <h4 class="text-center mb-3">🎟️ Please download your Tickets</h4>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Ticket No.</th>
                        <th>QR Preview</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $index => $member)
                        @php
                            $membershipNo = $member->membership_number;
                            $cardRelativePath = 'storage/raffle_cards/' . $membershipNo . '.png';
                            $cardFullPath = public_path($cardRelativePath);
                            clearstatcache();
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $membershipNo }}</td>
                            <td>
                                @if (file_exists($cardFullPath))
                                    <img src="{{ asset($cardRelativePath) }}" alt="Ticket QR" width="120">
                                @else
                                    <span class="text-danger">File Missing</span>
                                @endif
                            </td>
                            <td>
                                @if (file_exists($cardFullPath))
                                    <a href="{{ asset($cardRelativePath) }}" download class="btn btn-success btn-sm">
                                        ⬇️ Download
                                    </a>
                                @else
                                    <span class="text-muted">Unavailable</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
