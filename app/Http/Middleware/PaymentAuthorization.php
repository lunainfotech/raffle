<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class PaymentAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this resource.');
        }

        $user = Auth::user();
        
        // Check if user has a valid membership with completed payment
        $member = Member::where('email', $user->email)
                       ->where('payment_status', 'completed')
                       ->first();

        if (!$member) {
            return redirect()->route('payment.required')->with('error', 'Payment authorization required to access this resource.');
        }

        // Add member data to request for use in controllers
        $request->attributes->add(['authorized_member' => $member]);

        return $next($request);
    }
} 