<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Raffle App')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 font-sans">

    <header class="bg-white shadow">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">Raffle App</a>

            <nav class="space-x-4">
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-500">Home</a>
                <a href="{{ route('members.create') }}" class="text-gray-700 hover:text-blue-500">Register</a>

                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-500">Login</a>
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-blue-500">Sign Up</a>
                @else
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-500">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-red-500">Logout</button>
                    </form>
                @endguest
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-10 px-6">
        @yield('content')
    </main>

</body>
</html>
