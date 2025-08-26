<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        table.page {
            width: 100%;
            border-collapse: collapse;
            page-break-after: always;
        }

        td.ticket {
            width: 50%;
            padding: 5mm;
            vertical-align: top;
        }

        .ticket img {
            width: 100%;
            height: auto;
            display: block;
        }

        .ticket-label {
            text-align: center;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    @foreach($ticketGroups as $group)
    <table class="page">
        @foreach($group->chunk(2) as $pair)
        <tr>
            @foreach($pair as $ticketPath)
            @php
            $fileName = basename($ticketPath);
            $ticketId = pathinfo($fileName, PATHINFO_FILENAME);
            $imagePath = public_path('storage/' . $ticketPath);
            @endphp
            <td class="ticket">
                <div class="ticket">
                    <img src="{{ $imagePath }}" alt="Ticket {{ $ticketId }}">
                    <div class="ticket-label">{{ $ticketId }}</div>
                </div>
            </td>
            @endforeach

            @if($pair->count() < 2)
                <td class="ticket">
                </td> {{-- Fill empty cell if odd count --}}
                @endif
        </tr>
        @endforeach
    </table>
    @endforeach

</body>

</html>