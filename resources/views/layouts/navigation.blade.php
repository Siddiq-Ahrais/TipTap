<header class="bg-gray-900">
    <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">


        <a href="/" class="text-white font-bold text-xl">TipTap-DB</a>


        <div class="hidden lg:flex lg:gap-x-12">
            <a href="/" class="text-sm font-semibold leading-6 text-white">Home</a>
            <a href="/about" class="text-sm font-semibold leading-6 text-white">About</a>
            <a href="/materi" class="text-sm font-semibold leading-6 text-white">Materi</a>
        </div>


        <div class="text-white">
            @auth
                <span class="mr-3">Hello, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold leading-6 text-white">Logout</button>
                </form>
            @endauth
        </div>
    </nav>
</header>