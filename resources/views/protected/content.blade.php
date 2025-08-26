@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="text-6xl mb-4">🎉</div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome to Premium Content</h1>
                <p class="text-gray-600 text-lg">You have successfully completed payment authorization!</p>
            </div>

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
                            <p>Your payment has been verified and you now have access to all premium features.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-blue-900 mb-4">Exclusive Benefits</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-blue-800">Priority customer support</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-blue-800">Early access to new features</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-blue-800">Exclusive member discounts</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-blue-800">Advanced analytics dashboard</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-purple-50 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-purple-900 mb-4">Premium Features</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-purple-800">Unlimited access to content</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-purple-800">Download capabilities</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-purple-800">Advanced search filters</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-purple-800">Custom notifications</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Your Membership Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 border">
                        <h4 class="font-medium text-gray-900">Membership Number</h4>
                        <p class="text-gray-600">{{ $request->authorized_member->membership_number ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 border">
                        <h4 class="font-medium text-gray-900">Payment Status</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Authorized
                        </span>
                    </div>
                    <div class="bg-white rounded-lg p-4 border">
                        <h4 class="font-medium text-gray-900">Access Level</h4>
                        <p class="text-gray-600">Premium</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-center space-x-4">
                <a href="{{ route('payment.history') }}" 
                   class="bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-gray-700 transition duration-200">
                    View Payment History
                </a>
                <a href="{{ route('home') }}" 
                   class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 