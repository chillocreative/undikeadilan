<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UNDIKEADILAN')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-950 via-sky-900 to-blue-950 relative overflow-x-hidden">
    {{-- Pattern overlay --}}
    <div class="fixed inset-0 opacity-[0.04] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    {{-- Decorative blur circles --}}
    <div class="fixed -top-40 -right-40 h-96 w-96 rounded-full bg-sky-400/10 blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-40 -left-40 h-96 w-96 rounded-full bg-blue-400/10 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 min-h-screen flex flex-col">
        {{-- Admin button (top right) --}}
        <div class="absolute top-3 right-4 sm:right-6 z-20 flex items-center gap-3">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="text-sky-200 hover:text-white text-sm">Panel Admin</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sky-300/60 hover:text-white text-sm">Log Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-sky-100 text-sm font-medium transition-colors ring-1 ring-white/15">Admin</a>
            @endauth
        </div>

        {{-- Content --}}
        <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 py-6 sm:py-10">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="border-t border-white/10 bg-white/5 backdrop-blur-md mt-auto">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 text-center text-sky-300/40 text-xs">
                PARTI KEADILAN RAKYAT &copy; {{ date('Y') }}
            </div>
        </footer>
    </div>

    {{-- Toast notification --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:toast.window="message = $event.detail.message; type = $event.detail.type || 'success'; show = true; setTimeout(() => show = false, 4000)"
         x-show="show"
         x-transition
         class="fixed top-4 right-4 z-50 max-w-sm"
         style="display: none;">
        <div :class="type === 'error' ? 'bg-rose-500/90' : 'bg-emerald-500/90'"
             class="rounded-xl px-5 py-3 text-white text-sm shadow-xl backdrop-blur-md">
            <span x-text="message"></span>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
