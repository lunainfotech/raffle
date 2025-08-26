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
<script type="text/javascript" src="https://js.authorize.net/v1/Accept.js"></script>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Payment Form</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('authorize.pay') }}" method="POST" id="paymentForm">
                        @csrf
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="cardNumber" name="card_number" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="exp_month" class="form-label">Expiration Month</label>
                                <select class="form-control" id="expMonth" name="exp_month" required>
                                    <option value="">Select Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exp_year" class="form-label">Expiration Year</label>
                                <select class="form-control" id="expYear" name="exp_year" required>
                                    <option value="">Select Year</option>
                                    @for($i = date('Y'); $i <= date('Y') + 15; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required min="1" step="any">
                        </div>
                        <input type="hidden" name="dataDescriptor" id="dataDescriptor">
                        <input type="hidden" name="dataValue" id="dataValue">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const authData = {
        clientKey: "{{ config('services.authorizenet.client_key') }}",
        apiLoginID: "{{ config('services.authorizenet.login_id') }}"
    };

    const cardData = {
        cardNumber: document.getElementById("cardNumber").value,
        month: document.getElementById("expMonth").value,
        year: document.getElementById("expYear").value,
        cardCode: document.getElementById("cvv").value
    };

    Accept.dispatchData({
        authData,
        cardData
    }, function(response) {
        if (response.messages.resultCode === "Error") {
            alert("Error: " + response.messages.message[0].text);
        } else {
            document.getElementById("dataDescriptor").value = response.opaqueData.dataDescriptor;
            document.getElementById("dataValue").value = response.opaqueData.dataValue;
            document.getElementById("paymentForm").submit();
        }
    });
});
</script>
@endsection