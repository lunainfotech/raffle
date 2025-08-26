@extends('layouts.frontend')

@section('title', 'Welcome to Shree Ram lalla Rath Raffle')
@section('head')
<style>
    body {
        background:#ffe8b3!important;
    }

    nav {
        display: none !important;
    }

    main {
        padding: 0 !important;
    }
    .required-asterisk {
        color: #dc2626; /* Tailwind red-600 */
        font-weight: bold;
        margin-left: 2px;
    }
    /* Overlay styles */
    #submitOverlay {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, 0.85);
        align-items: center;
        justify-content: center;
    }
    #submitOverlay .spinner {
        border: 4px solid #e5e7eb;
        border-top: 4px solid #2563eb;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg);}
        100% { transform: rotate(360deg);}
    }
    #submitOverlay .text {
        margin-top: 1.5rem;
        font-size: 1.25rem;
        color: #1e293b;
        text-align: center;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-2 py-2 card">
    <img src="/image/ticket-banner.jpg" alt="Shree Ram Lalla Rath Raffle Ticket" class="rounded border shadow-sm border-dotted"/>

    @if ($errors->has('registration'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-6 text-center font-semibold">
        {{ $errors->first('registration') }}
    </div>
    @endif


    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <strong>There were some problems with your input:</strong>
        <ul class="mt-2 list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="paymentForm" action="{{ route('members.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- FIRST + LAST NAME --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">First Name<span class="required-asterisk">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Last Name<span class="required-asterisk">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- EMAIL --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Email<span class="required-asterisk">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
                <p id="email-error" class="text-red-500 text-sm mt-1" style="display: none;">Please enter a valid email.</p>
            </div>

            {{-- PHONE --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Phone<span class="required-asterisk">*</span></label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                    pattern="[0-9]{10,15}" inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    maxlength="15"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required title="Phone number must be digits only (10 to 15 digits)">
                <p id="phone-error" class="text-red-500 text-sm mt-1" style="display: none;">Phone must be 10–15 digits.</p>
            </div>
        </div>
        {{-- ADDRESS --}}
        <div>
            <label class="block text-gray-700 font-medium mb-1">Address<span class="required-asterisk">*</span></label>
            <textarea name="address" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                rows="3">{{ old('address') }}</textarea>
        </div>

        {{-- CITY / STATE --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">City<span class="required-asterisk">*</span></label>
                <input type="text" name="city" value="{{ old('city') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">State<span class="required-asterisk">*</span></label>
                <select name="state"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
                    <option value="">-- Select State --</option>
                    @foreach ([
                    'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
                    'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
                    'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
                    'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
                    'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
                    'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
                    'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
                    'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
                    'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
                    'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
                    'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
                    'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
                    'WI' => 'Wisconsin', 'WY' => 'Wyoming'
                    ] as $abbr => $name)
                    <option value="{{ $name }}" {{ old('state') == $name ? 'selected' : '' }}>{{ $name }}</option>

                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Zip<span class="required-asterisk">*</span></label>
                <input type="text" name="zip" value="{{ old('zip') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>

        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- REFERRED INFO --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Referred Chapter</label>
                <select name="referred_chapter_name"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                    <option value="">-- Select Chapter --</option>
                    @foreach([
                    'Atlanta',
                    'Boston',
                    'Chicago',
                    'Cincinnati',
                    'Connecticut',
                    'Dallas',
                    'Detroit',
                    'Florida',
                    'Houston',
                    'Indiana',
                    'Irvine, CA',
                    'Lehigh Valley',
                    'Los Angeles, CA',
                    'Metro DC / Maryland',
                    'Minnesota',
                    'New England',
                    'New Jersey',
                    'North Carolina',
                    'Pittsburgh',
                    'San Francisco Bay Area',
                    'Southern California',
                    'Staten Island',
                    'Virginia'
                    ] as $chapter)
                    <option value="{{ $chapter }}" {{ old('referred_chapter_name') == $chapter ? 'selected' : '' }}>{{ $chapter }}</option>
                    @endforeach
                </select>
            </div>


            <div>
                <label class="block text-gray-700 font-medium mb-1">Referred By</label>
                <input type="text" name="referred_by" value="{{ old('referred_by') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>
        </div>

        {{-- AMOUNT --}}

        <div class="bg-blue-50 border border-blue-300 rounded-lg px-5 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-800 font-semibold text-lg mb-1">Ticket Price:</label>
                <div class="text-2xl font-bold text-blue-700 mb-1">
                    ${{ number_format(config('membership.amount_dollars') , 2) }}
                </div>
                <input type="hidden" name="amount" value="{{ config('membership.amount_cents') }}">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Ticket Qyanitiy</label>
                <input type="number" name="qty" min="1" max="10" value="1" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Total Price</label>
                <div class="total text-2xl font-bold text-red-700 mb-1"></div>
            </div>
        </div>


        {{-- STRIPE --}}
<!--         <div>
            <label class="block text-gray-700 font-medium mb-1">Card Details<span class="required-asterisk">*</span></label>
            <div id="card-element"
                class="w-full border border-gray-300 rounded px-3 py-2 bg-white focus:outline-none focus:ring focus:ring-blue-200">
            </div>
            <div id="card-errors" class="text-red-500 text-sm mt-2"></div>
        </div>

        <input type="hidden" name="stripeToken" id="stripeToken"> -->
        <div class="mb-3">
            <label for="card_number" class="form-label">Card Number<span class="required-asterisk">*</span></label>
            <input type="text" class="form-control" id="cardNumber" name="card_number" required maxlength="19" inputmode="numeric" pattern="[0-9\s]{13,19}" placeholder="1234 5678 9012 3456" oninput="formatCardNumber(this)">
            <script>
                function formatCardNumber(input) {
                    let value = input.value.replace(/\D/g, '').substring(0,16);
                    let formatted = value.replace(/(.{4})/g, '$1 ').trim();
                    input.value = formatted;
                }
            </script>
        </div>
        <div class="flex items-center space-x-4 mb-3">
            <div class="flex-1">
                <label for="exp_month" class="form-label flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Exp Month<span class="required-asterisk">*</span>
                </label>
                <select class="form-control" id="expMonth" name="exp_month" required>
                    <option value="">Select Month</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1">
                <label for="exp_year" class="form-label flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Exp Year<span class="required-asterisk">*</span>
                </label>
                <select class="form-control" id="expYear" name="exp_year" required>
                    <option value="">Select Year</option>
                    @for($i = date('Y'); $i <= date('Y') + 15; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1">
                <label for="cvv" class="form-label flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0V5a4 4 0 00-8 0v2m8 0a4 4 0 01-8 0m8 0v2a4 4 0 01-8 0V7"/></svg>
                    CVV<span class="required-asterisk">*</span>
                </label>
                <input type="text" class="form-control" id="cvv" name="cvv" required>
            </div>
        </div>
        <input type="hidden" name="dataDescriptor" id="dataDescriptor">
        <input type="hidden" name="dataValue" id="dataValue">

        <div>
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition" id="submitBtn">
                Buy Ticket
            </button>
        </div>
    </form>
    <!-- Overlay for submit -->
    <div id="submitOverlay" class="flex flex-col">
        <div class="spinner"></div>
        <div class="text">Processing payment, please wait...</div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://js.authorize.net/v1/Accept.js"></script>
<script>
let isSubmitting = false;
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (isSubmitting) {
        return false;
    }
    isSubmitting = true;

    // Show overlay
    document.getElementById('submitOverlay').style.display = 'flex';

    // Optionally disable submit button
    document.getElementById('submitBtn').disabled = true;

    const authData = {
        clientKey: "{{ config('services.authorizenet.client_key') }}",
        apiLoginID: "{{ config('services.authorizenet.login_id') }}"
    };

    const cardData = {
        cardNumber: document.getElementById("cardNumber").value.replace(/\s+/g, ''),
        month: document.getElementById("expMonth").value,
        year: document.getElementById("expYear").value,
        cardCode: document.getElementById("cvv").value
    };

    Accept.dispatchData({
        authData,
        cardData
    }, function(response) {
        if (response.messages.resultCode === "Error") {
            // Hide overlay and re-enable submit
            document.getElementById('submitOverlay').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
            isSubmitting = false;
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