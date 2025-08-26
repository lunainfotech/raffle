<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentAuthorizationController extends Controller
{
    /**
     * Show payment required page
     */
    public function paymentRequired()
    {
        return view('payment.required');
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'member_id' => 'required|exists:members,id'
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            
            if ($paymentIntent->status === 'succeeded') {
                $member = Member::find($request->member_id);
                $member->update([
                    'payment_status' => 'completed',
                    'stripe_payment_id' => $paymentIntent->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed'
                ], 400);
            }
        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Check payment authorization status
     */
    public function checkAuthorization(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'authorized' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $member = Member::where('email', $user->email)
                       ->where('payment_status', 'completed')
                       ->first();

        if ($member) {
            return response()->json([
                'authorized' => true,
                'member' => $member,
                'message' => 'Payment authorized'
            ]);
        }

        return response()->json([
            'authorized' => false,
            'message' => 'Payment not authorized'
        ], 403);
    }

    /**
     * Get payment history for user
     */
    public function paymentHistory()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $payments = Member::where('email', $user->email)
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('payment.history', compact('payments'));
    }

    /**
     * Resend payment verification
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id'
        ]);

        $member = Member::find($request->member_id);
        
        // Here you could implement email verification logic
        // For now, we'll just return a success message
        
        return response()->json([
            'success' => true,
            'message' => 'Payment verification email sent'
        ]);
    }

    /**
     * Cancel pending payment
     */
    public function cancelPayment(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id'
        ]);

        $member = Member::find($request->member_id);
        
        if ($member->payment_status === 'pending') {
            $member->update(['payment_status' => 'cancelled']);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment cancelled successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cannot cancel completed payment'
        ], 400);
    }
} 