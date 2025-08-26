@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Authorization Status</h1>
                <p class="text-gray-600">Check your payment status and manage your authorizations</p>
            </div>

            @if($hasCompletedPayment)
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Payment Authorized</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>You have completed payment authorization and have access to all premium features.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Payment Authorization Required</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>You need to complete payment authorization to access premium features.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @if($completedPayments->count() > 0)
                    <div class="bg-green-50 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-green-900 mb-4">✅ Completed Payments</h3>
                        <div class="space-y-3">
                            @foreach($completedPayments as $payment)
                                <div class="bg-white rounded-lg p-4 border border-green-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-green-900">{{ $payment->membership_number }}</h4>
                                            <p class="text-sm text-green-700">{{ $payment->first_name }} {{ $payment->last_name }}</p>
                                            <p class="text-sm text-green-600">${{ number_format($payment->amount / 100, 2) }}</p>
                                            <p class="text-xs text-green-500">{{ $payment->created_at->format('M j, Y') }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('members.receipt', $payment) }}" 
                                               class="text-green-600 hover:text-green-800 text-sm" target="_blank">
                                                Receipt
                                            </a>
                                            <a href="{{ route('members.show', $payment) }}" 
                                               class="text-green-600 hover:text-green-800 text-sm">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($pendingPayments->count() > 0)
                    <div class="bg-yellow-50 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-yellow-900 mb-4">⏳ Pending Payments</h3>
                        <div class="space-y-3">
                            @foreach($pendingPayments as $payment)
                                <div class="bg-white rounded-lg p-4 border border-yellow-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-yellow-900">{{ $payment->membership_number ?? 'Pending' }}</h4>
                                            <p class="text-sm text-yellow-700">{{ $payment->first_name }} {{ $payment->last_name }}</p>
                                            <p class="text-sm text-yellow-600">${{ number_format($payment->amount / 100, 2) }}</p>
                                            <p class="text-xs text-yellow-500">{{ $payment->created_at->format('M j, Y') }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="resendVerification({{ $payment->id }})" 
                                                    class="text-yellow-600 hover:text-yellow-800 text-sm">
                                                Resend
                                            </button>
                                            <button onclick="cancelPayment({{ $payment->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-center space-x-4">
                @if(!$hasCompletedPayment)
                    <a href="{{ route('members.create') }}" 
                       class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                        Complete Payment Authorization
                    </a>
                @else
                    <a href="{{ route('protected.content') }}" 
                       class="bg-green-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-green-700 transition duration-200">
                        Access Premium Content
                    </a>
                @endif
                
                <a href="{{ route('payment.history') }}" 
                   class="bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-700 transition duration-200">
                    View Full History
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function resendVerification(memberId) {
    if (confirm('Are you sure you want to resend the payment verification?')) {
        fetch('/members/payment/resend-verification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                member_id: memberId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment verification email sent successfully!');
                location.reload();
            } else {
                alert('Failed to send verification email: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending verification email.');
        });
    }
}

function cancelPayment(memberId) {
    if (confirm('Are you sure you want to cancel this payment? This action cannot be undone.')) {
        fetch('/payment/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                member_id: memberId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment cancelled successfully!');
                location.reload();
            } else {
                alert('Failed to cancel payment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling payment.');
        });
    }
}
</script>
@endsection 