<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .receipt-details p {
            margin: 5px 0;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registration Receipt</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="receipt-details">
        <p><strong>Receipt Number:</strong> {{ $member->stripe_payment_id }}</p>
        <p><strong>Date:</strong> {{ $member->created_at->format('F j, Y') }}</p>
        <p><strong>Membership Number:</strong> {{ $member->membership_number }}</p>
        <p><strong>Member Name:</strong> {{ $member->first_name }} {{ $member->last_name }}</p>
        <p><strong>Email:</strong> {{ $member->email }}</p>
    </div>

    <div class="amount">
        Amount Paid: $5,000.00
    </div>

    <div class="receipt-details">
        <p><strong>Payment Method:</strong> Credit Card</p>
        <p><strong>Payment Status:</strong> Completed</p>
    </div>

    <div class="footer">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p>Thank you for your registration!</p>
    </div>
</body>
</html> 