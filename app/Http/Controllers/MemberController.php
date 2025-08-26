<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\MemberRegistrationConfirmation;
use Illuminate\Support\Str;
use PDF;
use Stripe\Stripe;
use Stripe\Charge;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{

    public function createStripe()
    {
        $lastDate = Carbon::parse(env('LAST_DATE'));

        // If registration date is passed
        if (now()->gt($lastDate)) {
            return view('members.closed')->with('message', '🕒 Registration deadline has passed.');
        }

        // If max tickets reached
        $maxTickets = (int) env('MAX_TICKET', 2000);
        $currentRegistrations = Member::where('payment_status', 'completed')->count();

        if ($currentRegistrations >= $maxTickets) {
            return view('members.closed')->with('message', '🎟️ All spots are filled. Registration is now closed.');
        }

        // If all good, show the form
        return view('members.create');
    }


    public function storeStripe(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string|max:10',
            'amount' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1|max:10',
            'stripeToken' => 'required|string',
        ]);

        // 🛑 Ticket limit enforcement
        $maxTickets = (int) env('MAX_TICKET', 2000);
        $currentRegistrations = Member::where('payment_status', 'completed')->count();

        if ($currentRegistrations >= $maxTickets) {
            return redirect()->route('members.create')->withErrors([
                'registration' => '🎟️ Sorry, the maximum number of registrations (' . $maxTickets . ') has been reached.'
            ]);
        }

        // ✅ ADD THIS RIGHT HERE
        $existingTickets = Member::where('email', $request->email)->count();
        if (($existingTickets + $request->qty) > 10) {
            return redirect()->route('members.create')->withErrors([
                'email' => '❌ This email has already used ' . $existingTickets . ' tickets. You can only purchase a total of 10 tickets per email address.',
            ]);
        }


        try {
            $members = [];
            $qty = $request->qty;

            Stripe::setApiKey(config('services.stripe.secret'));

            $uuid = (string) Str::uuid();
            //$membershipNumber = 'MBR-' . strtoupper(Str::random(6));
            $membershipNumber = 'SRRR' . str_pad(Member::count() + 1, 4, '0', STR_PAD_LEFT);

            $amount = env('MEMBERSHIP_AMOUNT', 500); //intval($request->amount);

            $customer = \Stripe\Customer::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'source' => $request->stripeToken,
            ]);

            // Stripe expects the amount to be in cents (integer)
            $totalCharge = (500 * 100) * $qty; // Amount in cents

            $charge = \Stripe\Charge::create([
                'amount' => $totalCharge, // Charge for all tickets
                'currency' => 'usd',
                'description' => 'VHPA Raffle Registration Fee (x' . $qty . ')',
                'customer' => $customer->id, // THIS LINE is critical
            ]);

            if (isset($charge->status) && $charge->status === 'succeeded') {

                for ($i = 1; $i <= $qty; $i++) {
                    $uuid = (string) Str::uuid();
    
                    $member = Member::create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address,
                        'city' => $request->city,
                        'state' => $request->state,
                        'referred_chapter_name' => $request->referred_chapter_name ?? '',
                        'referred_by' => $request->referred_by ?? '',
                        'uuid' => $uuid,
                        'zip' => $request->zip,
                        'membership_number' => null,
                        'amount' => $amount,
                        'payment_status' => 'completed',
                        'stripe_payment_id' => $charge->id,
                        'is_email_sent' => false
                    ]);
    
                    $membershipNumber = 'SRRR' . str_pad($member->id, 4, '0', STR_PAD_LEFT);
                    $member->membership_number = $membershipNumber;
                    $member->save();
    
                    // QR code generation
                    $qrUrl = url('/member/' . $member->uuid);
                    $qrImage = QrCode::format('png')->size(300)->color(93, 0, 0)->generate($qrUrl);
                    $qrPathRel = 'qrcodes/' . $membershipNumber . '.png';
                    $qrPathAbs = storage_path('app/public/' . $qrPathRel);
                    Storage::disk('public')->put($qrPathRel, $qrImage);
                    $member->qr_code = $qrPathRel;
                    $member->save();
    
                    // Raffle card image
                    $baseImage = Image::make(public_path('raffle-assets/2.png'));
                    $qrSize = 450;
                    $qr = Image::make($qrPathAbs)->resize($qrSize, $qrSize);
                    $insertX = 1135 + (510 - $qrSize) / 2;
                    $insertY = 15 + (510 - ($qrSize + 40)) / 2;
    
                    $baseImage->insert($qr, 'top-left', intval($insertX), intval($insertY));
                    $baseImage->text($membershipNumber, intval(1135 + 510 / 2), intval($insertY + $qrSize + 5), function ($font) {
                        $font->file(public_path('fonts/arialbd.ttf'));
                        $font->size(30);
                        $font->color('#FFFFFF');
                        $font->align('center');
                        $font->valign('top');
                    });
                    $baseImage->text($membershipNumber, 35, 470, function ($font) {
                        $font->file(public_path('fonts/arialbd.ttf'));
                        $font->size(36);
                        $font->color('#000000');
                        $font->align('left');
                        $font->valign('top');
                    });
    
                    $finalPath = storage_path('app/public/raffle_cards/' . $membershipNumber . '.png');
                    $baseImage->save($finalPath);
    
                    // Attach path to array
                    $member->raffle_card_path = $finalPath;
                    $members[] = $member;
                }
    
                try {
                    Mail::to($members[0]->email)->send(new MemberRegistrationConfirmation($members, $totalCharge));
                }
                catch (\Exception $e) {
                    Log::error('Failed to send registration confirmation email', [
                        'error' => $e->getMessage(),
                        'member_email' => $members[0]->email ?? null,
                    ]);
                }
    
                return view('members.success', compact('members', 'charge', 'totalCharge'));

            } else {
                // Log Stripe response for debugging
                Log::error('Stripe payment failed', ['charge' => $charge ? $charge->toArray() : null]);
                return back()->with('error', 'Payment was not successful. Please try again.');
            }

            
        } catch (\Exception $e) {
            Log::error('Stripe payment failed', [
                'error' => $e->getMessage(),
                'all_request' => request()->all(),
            ]);
            $errorMessage = 'Something went wrong with your payment. Please try again.';
            return view('members.failure', ['error' => $errorMessage]);
        }
    }


    public function viewByUuid($uuid)
    {
        $member = Member::where('uuid', $uuid)->firstOrFail();

        $raffleCardPath = storage_path('app/public/raffle_cards/' . $member->membership_number . '.png');

        if (!file_exists($raffleCardPath)) {
            //regenerate
            // Raffle card image
            $membershipNumber = $member->membership_number;
            $qrPathRel = 'qrcodes/' . $membershipNumber . '.png';
            $qrPathAbs = storage_path('app/public/' . $qrPathRel);
            $baseImage = Image::make(public_path('raffle-assets/2.png'));
            $qrSize = 450;
            $qr = Image::make($qrPathAbs)->resize($qrSize, $qrSize);
            $insertX = 1135 + (510 - $qrSize) / 2;
            $insertY = 15 + (510 - ($qrSize + 40)) / 2;

            $baseImage->insert($qr, 'top-left', intval($insertX), intval($insertY));
            $baseImage->text($membershipNumber, intval(1135 + 510 / 2), intval($insertY + $qrSize + 5), function ($font) {
                $font->file(public_path('fonts/arialbd.ttf'));
                $font->size(30);
                $font->color('#FFFFFF');
                $font->align('center');
                $font->valign('top');
            });
            $baseImage->text($membershipNumber, 35, 470, function ($font) {
                $font->file(public_path('fonts/arialbd.ttf'));
                $font->size(36);
                $font->color('#000000');
                $font->align('left');
                $font->valign('top');
            });

            $finalPath = storage_path('app/public/raffle_cards/' . $membershipNumber . '.png');
            $baseImage->save($finalPath);
        }

        return view('members.qr_view', compact('member'));
    }


    public function show(Member $member)
    {
        return view('members.show', compact('member'));
    }

    public function receipt(Member $member)
    {
        $pdf = PDF::loadView('members.receipt', compact('member'));
        return $pdf->download('receipt-' . $member->membership_number . '.pdf');
    }

    /**
     * Check if user has payment authorization
     */
    public function checkPaymentAuthorization()
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
     * Get payment authorization status for current user
     */
    public function getPaymentStatus()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $members = Member::where('email', $user->email)
                        ->orderBy('created_at', 'desc')
                        ->get();

        $hasCompletedPayment = $members->where('payment_status', 'completed')->count() > 0;
        $pendingPayments = $members->where('payment_status', 'pending');
        $completedPayments = $members->where('payment_status', 'completed');

        return view('members.payment-status', compact('hasCompletedPayment', 'pendingPayments', 'completedPayments'));
    }

    /**
     * Resend payment verification email
     */
    public function resendPaymentVerification(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id'
        ]);

        $member = Member::find($request->member_id);
        
        if ($member->payment_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending payments can be verified'
            ], 400);
        }

        try {
            // Here you would implement the actual email verification logic
            // For now, we'll just return a success message
            
            return response()->json([
                'success' => true,
                'message' => 'Payment verification email sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage()
            ], 500);
        }
    }

    /*************************************/
    /*************************************/
    /*************************************/
    /*************************************/

    public function createAuthorize()
    {
        $lastDate = Carbon::parse(env('LAST_DATE'));

        // If registration date is passed
        if (now()->gt($lastDate)) {
            return view('members.closed')->with('message', '🕒 Registration deadline has passed.');
        }

        // If max tickets reached
        $maxTickets = (int) env('MAX_TICKET', 2000);
        $currentRegistrations = Member::where('payment_status', 'completed')->count();

        if ($currentRegistrations >= $maxTickets) {
            return view('members.closed')->with('message', '🎟️ All spots are filled. Registration is now closed.');
        }

        // If all good, show the form
        return view('members.createAuth');
    }


    public function storeAuthorize(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string|max:10',
            'amount' => 'required|numeric|min:1',
            'qty' => 'required|integer|min:1|max:10',
            'dataDescriptor' => 'required|string',
            'dataValue' => 'required|string',
        ]);

        // 🛑 Ticket limit enforcement
        $maxTickets = (int) env('MAX_TICKET', 2000);
        $currentRegistrations = Member::where('payment_status', 'completed')->count();

        if ($currentRegistrations >= $maxTickets) {
            return redirect()->route('members.create')->withErrors([
                'registration' => '🎟️ Sorry, the maximum number of registrations (' . $maxTickets . ') has been reached.'
            ]);
        }

        // ✅ ADD THIS RIGHT HERE
        $existingTickets = Member::where('email', $request->email)->count();
        if (($existingTickets + $request->qty) > 10) {
            return redirect()->route('members.create')->withErrors([
                'email' => '❌ This email has already used ' . $existingTickets . ' tickets. You can only purchase a total of 10 tickets per email address.',
            ]);
        }


        try {
            $members = [];
            $qty = $request->qty;

            // Debug: Log the config values to help diagnose authentication issues
            $loginId = config('services.authorizenet.login_id');
            $transactionKey = config('services.authorizenet.transaction_key');
            $sandbox = config('services.authorizenet.sandbox', true);

            if (empty($loginId) || empty($transactionKey)) {
                return back()->with('error', 'Payment gateway credentials are not set. Please contact the administrator.');
            }

            $uuid = (string) Str::uuid();
            //$membershipNumber = 'MBR-' . strtoupper(Str::random(6));
            $membershipNumber = 'SRRR' . str_pad(Member::count() + 1, 4, '0', STR_PAD_LEFT);

            $amount = env('MEMBERSHIP_AMOUNT', 500); //intval($request->amount);
            $totalCharge = $amount * $qty;


            // Log::info("Authorize.Net login_id: $loginId, transaction_key: $transactionKey, sandbox: $sandbox");

            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($loginId);
            $merchantAuthentication->setTransactionKey($transactionKey);

            $opaqueData = new AnetAPI\OpaqueDataType();
            $opaqueData->setDataDescriptor($request->input('dataDescriptor'));
            $opaqueData->setDataValue($request->input('dataValue'));

            $paymentType = new AnetAPI\PaymentType();
            $paymentType->setOpaqueData($opaqueData);

            // ✅ Add Customer Info
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setType("individual");
            $customerData->setEmail($request->email);

            $billTo = new AnetAPI\CustomerAddressType();
            $billTo->setFirstName($request->first_name);
            $billTo->setLastName($request->last_name);
            $billTo->setAddress($request->address);
            $billTo->setCity($request->city);
            $billTo->setState($request->state);
            $billTo->setZip($request->zip);
            $billTo->setCountry('US');

            $transactionRequest = new AnetAPI\TransactionRequestType();
            $transactionRequest->setTransactionType("authCaptureTransaction");
            $transactionRequest->setAmount( $totalCharge );
            $transactionRequest->setPayment($paymentType);
            $transactionRequest->setCustomer($customerData);
            $transactionRequest->setBillTo($billTo);

            $requestObj = new AnetAPI\CreateTransactionRequest();
            $requestObj->setMerchantAuthentication($merchantAuthentication);
            $requestObj->setTransactionRequest($transactionRequest);

            $controller = new AnetController\CreateTransactionController($requestObj);

            $response = $controller->executeWithApiResponse(
                config('services.authorizenet.sandbox') 
                    ? \net\authorize\api\constants\ANetEnvironment::SANDBOX 
                    : \net\authorize\api\constants\ANetEnvironment::PRODUCTION
            );


            if ($response !== null && $response->getMessages()->getResultCode() === "Ok") {
                $transactionId = $response->getTransactionResponse()->getTransId();

                for ($i = 1; $i <= $qty; $i++) {
                    $uuid = (string) Str::uuid();
    
    
                    $member = Member::create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address,
                        'city' => $request->city,
                        'state' => $request->state,
                        'referred_chapter_name' => $request->referred_chapter_name ?? '',
                        'referred_by' => $request->referred_by ?? '',
                        'uuid' => $uuid,
                        'zip' => $request->zip,
                        'membership_number' => null,
                        'amount' => $amount,
                        'payment_status' => 'completed',
                        'stripe_payment_id' => $transactionId,
                        'is_email_sent' => false
                    ]);
    
                    $membershipNumber = 'SRRR' . str_pad($member->id, 4, '0', STR_PAD_LEFT);
                    $member->membership_number = $membershipNumber;
                    $member->save();
    
                    // QR code generation
                    $qrUrl = url('/member/' . $member->uuid);
                    $qrImage = QrCode::format('png')->size(300)->color(93, 0, 0)->generate($qrUrl);
                    $qrPathRel = 'qrcodes/' . $membershipNumber . '.png';
                    $qrPathAbs = storage_path('app/public/' . $qrPathRel);
                    Storage::disk('public')->put($qrPathRel, $qrImage);
                    $member->qr_code = $qrPathRel;
                    $member->save();
    
                    // Raffle card image
                    $baseImage = Image::make(public_path('raffle-assets/2.png'));
                    $qrSize = 450;
                    $qr = Image::make($qrPathAbs)->resize($qrSize, $qrSize);
                    $insertX = 1135 + (510 - $qrSize) / 2;
                    $insertY = 15 + (510 - ($qrSize + 40)) / 2;
    
                    $baseImage->insert($qr, 'top-left', intval($insertX), intval($insertY));
                    $baseImage->text($membershipNumber, intval(1135 + 510 / 2), intval($insertY + $qrSize + 5), function ($font) {
                        $font->file(public_path('fonts/arialbd.ttf'));
                        $font->size(30);
                        $font->color('#FFFFFF');
                        $font->align('center');
                        $font->valign('top');
                    });
                    $baseImage->text($membershipNumber, 35, 470, function ($font) {
                        $font->file(public_path('fonts/arialbd.ttf'));
                        $font->size(36);
                        $font->color('#000000');
                        $font->align('left');
                        $font->valign('top');
                    });
    
                    $finalPath = storage_path('app/public/raffle_cards/' . $membershipNumber . '.png');
                    $baseImage->save($finalPath);
    
                    // Attach path to array
                    $member->raffle_card_path = $finalPath;
                    $members[] = $member;
                }

                Mail::to($members[0]->email)->send(new MemberRegistrationConfirmation($members, $totalCharge));
                return view('members.success', compact('members','totalCharge'));

            } else {
                // Log the error for debugging
                \Log::error('Authorize.Net payment error: ' . $response->getMessages()->getMessage()[0]->getText());

                // Send a generic, authentic message to the user
                return back()->with('error', 'We were unable to process your payment at this time. Please check your card details or try again later.');
            }
        } catch (\Exception $e) {
            \Log::error('Authorize.Net exception: ' . $e->getMessage());
            return view('members.failure', ['error' => 'We were unable to process your payment at this time. Please try again later or contact support.']);
        }
    }

    public function createMemberOffline()
    {
        $lastDate = Carbon::parse(env('LAST_DATE'));

        // If registration date is passed
        if (now()->gt($lastDate)) {
            return view('members.closed')->with('message', '🕒 Registration deadline has passed.');
        }

        // If max tickets reached
        $maxTickets = (int) env('MAX_TICKET', 2000);
        $currentRegistrations = Member::where('payment_status', 'completed')->count();

        if ($currentRegistrations >= $maxTickets) {
            return view('members.closed')->with('message', '🎟️ All spots are filled. Registration is now closed.');
        }

        // If all good, show the form
        return view('members.create-offline');
    }

    public function storeMemberOffline(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string|max:10',
            'amount' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1|max:10',
        ]);

        // 🛑 Ticket limit enforcement
        $maxTickets = (int) env('MAX_TICKET', 2000);
        $currentRegistrations = Member::where('payment_status', 'completed')->count();

        if ($currentRegistrations >= $maxTickets) {
            return redirect()->route('members.create')->withErrors([
                'registration' => '🎟️ Sorry, the maximum number of registrations (' . $maxTickets . ') has been reached.'
            ]);
        }

        // ✅ ADD THIS RIGHT HERE
        $existingTickets = Member::where('email', $request->email)->count();
        if (($existingTickets + $request->qty) > 10) {
            return redirect()->route('members.create')->withErrors([
                'email' => '❌ This email has already used ' . $existingTickets . ' tickets. You can only purchase a total of 10 tickets per email address.',
            ]);
        }


        try {
            $members = [];
            $qty = $request->qty;

            Stripe::setApiKey(config('services.stripe.secret'));

            $uuid = (string) Str::uuid();
            //$membershipNumber = 'MBR-' . strtoupper(Str::random(6));
            $membershipNumber = 'SRRR' . str_pad(Member::count() + 1, 4, '0', STR_PAD_LEFT);

            $amount = env('MEMBERSHIP_AMOUNT', 500); //intval($request->amount);
            
            $totalCharge = 500* $qty; // Amount in cents

            for ($i = 1; $i <= $qty; $i++) {
                $uuid = (string) Str::uuid();

                $member = Member::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'referred_chapter_name' => $request->referred_chapter_name ?? '',
                    'referred_by' => $request->referred_by ?? '',
                    'uuid' => $uuid,
                    'zip' => $request->zip,
                    'membership_number' => null,
                    'amount' => $amount,
                    'payment_status' => 'completed',
                    'stripe_payment_id' => null,
                    'is_email_sent' => false
                ]);

                $membershipNumber = 'SRRR' . str_pad($member->id, 4, '0', STR_PAD_LEFT);
                $member->membership_number = $membershipNumber;
                $member->save();

                // QR code generation
                $qrUrl = url('/member/' . $member->uuid);
                $qrImage = QrCode::format('png')->size(300)->color(93, 0, 0)->generate($qrUrl);
                $qrPathRel = 'qrcodes/' . $membershipNumber . '.png';
                $qrPathAbs = storage_path('app/public/' . $qrPathRel);
                Storage::disk('public')->put($qrPathRel, $qrImage);
                $member->qr_code = $qrPathRel;
                $member->save();

                // Raffle card image
                $baseImage = Image::make(public_path('raffle-assets/2.png'));
                $qrSize = 450;
                $qr = Image::make($qrPathAbs)->resize($qrSize, $qrSize);
                $insertX = 1135 + (510 - $qrSize) / 2;
                $insertY = 15 + (510 - ($qrSize + 40)) / 2;

                $baseImage->insert($qr, 'top-left', intval($insertX), intval($insertY));
                $baseImage->text($membershipNumber, intval(1135 + 510 / 2), intval($insertY + $qrSize + 5), function ($font) {
                    $font->file(public_path('fonts/arialbd.ttf'));
                    $font->size(30);
                    $font->color('#FFFFFF');
                    $font->align('center');
                    $font->valign('top');
                });
                $baseImage->text($membershipNumber, 35, 470, function ($font) {
                    $font->file(public_path('fonts/arialbd.ttf'));
                    $font->size(36);
                    $font->color('#000000');
                    $font->align('left');
                    $font->valign('top');
                });

                $finalPath = storage_path('app/public/raffle_cards/' . $membershipNumber . '.png');
                $baseImage->save($finalPath);

                // Attach path to array
                $member->raffle_card_path = $finalPath;
                $members[] = $member;
            }

            try {
                Mail::to($members[0]->email)->send(new MemberRegistrationConfirmation($members, $totalCharge));
                Log::info('Successfully sent registration confirmation email', [
                    'member_email' => $members[0]->email ?? null,
                ]);
            }
            catch (\Exception $e) {
                Log::error('Failed to send registration confirmation email', [
                    'error' => $e->getMessage(),
                    'member_email' => $members[0]->email ?? null,
                ]);
            }
            $charge = null;
            return view('members.success', compact('members', 'charge', 'totalCharge'));
            
        } catch (\Exception $e) {
            Log::error('Stripe payment failed', [
                'error' => $e->getMessage(),
                'all_request' => request()->all(),
            ]);
            $errorMessage = 'Something went wrong. Please try again.';
            return view('members.failure', ['error' => $errorMessage]);
        }
    }
}
