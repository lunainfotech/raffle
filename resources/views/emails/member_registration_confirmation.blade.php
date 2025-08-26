<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shree Ram Rath Raffle Ticket Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2>Namaste {{ $members[0]->first_name }} {{ $members[0]->last_name }},</h2>

    <p>On behalf of Vishwa Hindu Parishad of America, we extend our heartfelt gratitude to you for supporting the Ram Rath Raffle. Your generous contribution not only brings you closer to the sacred yatra of Shri Ram but also strengthens our collective efforts to promote dharmic values and community service across the nation.</p>
    <p>Every ticket purchased is a step towards preserving and celebrating our rich heritage. We are honored to have your support on this meaningful journey.</p>
    <p>Your ticket(s) has(ve) been successfully confirmed.</p>
    <ul>
        <li><strong>Name:</strong> {{ $members[0]->first_name }} {{ $members[0]->last_name }}</li>
        <li><strong>City:</strong> {{ $members[0]->city }}, {{ $members[0]->state }}, {{$members[0]->zip }},</li>
        <li><strong>Email:</strong> {{ $members[0]->email }}</li>
        <li><strong>Total Tickets:</strong> {{ count($members) }}</li>
        <li><strong>Total Amount Paid:</strong> ${{ number_format( count($members) * 500, 2) }}</li>
    </ul>
    <p><strong>Your Ticket ID(s)</strong></p>
    <ol>
        @foreach( $members as $member)
            <li>{{ $member->membership_number }}</li>
        @endforeach
    </ol>
    <hr>
    <p>All ticket(s) is/are attached <strong>raffle card</strong> with this email.</p>

    <p>May Bhagwan Shri Ram shower you with blessings and guide your path with light and strength.</p>

    <p>Jai Shri Ram! <br>
    VHPA Ram Rath Raffle Team</p>
</body>
</html>
