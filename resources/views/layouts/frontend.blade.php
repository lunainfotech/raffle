<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title') | Raffle</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('head')
    <style>
        a {
            text-decoration:none;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">
<marquee style="background: #7d0404;
    padding: 5px;
    color: #fff900;">Shree Ram Jai Ram Jai Jai Ram | Shree Ram Jai Ram Jai Jai Ram | Shree Ram Jai Ram Jai Jai Ram | Shree Ram Jai Ram Jai Jai Ram | Shree Ram Jai Ram Jai Jai Ram | Shree Ram Jai Ram Jai Jai Ram | Shree Ram Jai Ram Jai Jai Ram | Shree Ram Jai Ram Jai Jai Ram </marquee>
    {{-- HEADER --}}
    @include('layouts.header')

    {{-- MAIN CONTENT --}}
    <main class="py-6">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('layouts.footer')


    @yield('scripts')
</body>

</html>