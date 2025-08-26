<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center space-x-3">
            <img src="https://rammandir2024.org/wp-content/uploads/2023/11/raam-logo.png" alt="VHPA Raffle Logo" class="h-12 w-auto">
            <span class="text-xl font-bold text-blue-800">VHPA Raffle</span>
        </a>

        <div class="hidden md:flex items-center space-x-6">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-blue-700 font-semibold underline' : 'text-gray-700 hover:text-blue-600' }}">
                Home
            </a>
            <a href="{{ route('members.create') }}" class="{{ request()->is('register') ? 'text-blue-700 font-semibold underline' : 'text-gray-700 hover:text-blue-600' }}">
                Register
            </a>
        </div>

        {{-- Mobile menu toggle --}}
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-gray-600 focus:outline-none">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile dropdown --}}
    <div id="mobile-menu" class="hidden md:hidden bg-white px-4 pb-4 space-y-2">
        <a href="{{ url('/') }}" class="block py-2 text-gray-700 hover:text-blue-600 {{ request()->is('/') ? 'font-semibold underline' : '' }}">Home</a>
        <a href="{{ route('members.create') }}" class="block py-2 text-gray-700 hover:text-blue-600 {{ request()->is('register') ? 'font-semibold underline' : '' }}">Register</a>
    </div>

    <script>
        document.getElementById('mobile-menu-button')?.addEventListener('click', () => {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</nav>
