<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Illuminate\Support\Facades\Log;

class AuthorizePaymentController extends Controller
{
    public function showForm()
    {
        return view('frontend.payment');
    }

    public function makePayment(Request $request)
    {

        // INSERT_YOUR_CODE
        // Send a test email
        \Mail::raw('This is a test email from AuthorizePaymentController.', function ($message) {
            $message->to('cdbhushan@gmail.com')
                    ->subject('Test Email from AuthorizePaymentController');
        });


        $request->validate([
            'dataDescriptor' => 'required|string',
            'dataValue' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        // Debug: Log the config values to help diagnose authentication issues
        $loginId = config('services.authorizenet.login_id');
        $transactionKey = config('services.authorizenet.transaction_key');
        $sandbox = config('services.authorizenet.sandbox', true);

        if (empty($loginId) || empty($transactionKey)) {
            return back()->with('error', 'Payment gateway credentials are not set. Please contact the administrator.');
        }

        // Log::info("Authorize.Net login_id: $loginId, transaction_key: $transactionKey, sandbox: $sandbox");

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($loginId);
        $merchantAuthentication->setTransactionKey($transactionKey);

        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($request->input('dataDescriptor'));
        $opaqueData->setDataValue($request->input('dataValue'));

        $paymentType = new AnetAPI\PaymentType();
        $paymentType->setOpaqueData($opaqueData);

        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType("authCaptureTransaction");
        $transactionRequest->setAmount($request->input('amount'));
        $transactionRequest->setPayment($paymentType);

        $requestObj = new AnetAPI\CreateTransactionRequest();
        $requestObj->setMerchantAuthentication($merchantAuthentication);
        $requestObj->setTransactionRequest($transactionRequest);

        $environment = $sandbox ? ANetEnvironment::SANDBOX : ANetEnvironment::PRODUCTION;

        $controller = new AnetController\CreateTransactionController($requestObj);
        $response = $controller->executeWithApiResponse($environment);

        if ($response !== null) {
            $resultCode = $response->getMessages()->getResultCode();
            if ($resultCode === "Ok" && $response->getTransactionResponse() && $response->getTransactionResponse()->getTransId()) {
                $transId = $response->getTransactionResponse()->getTransId();
                return back()->with('success', 'Transaction Successful: ' . $transId);
            } else {
                $error = 'Unknown error';
                // Check for transaction response errors
                if ($response->getTransactionResponse() && $response->getTransactionResponse()->getErrors()) {
                    $errors = $response->getTransactionResponse()->getErrors();
                    $error = $errors[0]->getErrorText();
                }
                // Check for general API errors
                elseif ($response->getMessages() && $response->getMessages()->getMessage()) {
                    $error = $response->getMessages()->getMessage()[0]->getText();
                }
                // Special handling for authentication errors
                if (stripos($error, 'authentication') !== false) {
                    $error .= ' Please check your Authorize.Net API Login ID and Transaction Key. If you are using sandbox mode, ensure you are using sandbox credentials.';
                }
                return back()->with('error', 'Error: ' . $error);
            }
        } else {
            return back()->with('error', 'No response from payment gateway.');
        }
    }
}