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

    <form id="payment-form" action="{{ route('members.store.offline') }}" method="POST" class="space-y-6">
        @csrf

        {{-- FIRST + LAST NAME --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">First Name*</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Last Name*</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- EMAIL --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Email*</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
                <p id="email-error" class="text-red-500 text-sm mt-1" style="display: none;">Please enter a valid email.</p>
            </div>

            {{-- PHONE --}}
            <div>
                <label class="block text-gray-700 font-medium mb-1">Phone*</label>
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
            <label class="block text-gray-700 font-medium mb-1">Address*</label>
            <textarea name="address" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                rows="3">{{ old('address') }}</textarea>
        </div>

        {{-- CITY / STATE --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">City*</label>
                <input type="text" name="city" value="{{ old('city') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">State*</label>
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
                <label class="block text-gray-700 font-medium mb-1">Zip*</label>
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

        <div>
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition">
                Register Ticket
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qtyInput = document.querySelector('input[name="qty"]');
        const totalDiv = document.querySelector('.total');
        const ticketPrice = 500;

        function updateTotal() {
            let quantity = parseInt(qtyInput.value) || 1;

            // Clamp value between 1 and 10
            if (quantity < 1) quantity = 1;
            if (quantity > 10) quantity = 10;

            // Update input value if it was invalid
            qtyInput.value = quantity;

            // Calculate and display total
            const total = ticketPrice * quantity;
            totalDiv.textContent = "$" + total.toFixed(2);
        }

        // Initial total calculation
        updateTotal();

        // Validate and update total when quantity changes
        qtyInput.addEventListener('input', updateTotal);
    });
</script>
@endsection