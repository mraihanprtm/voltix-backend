<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Jika title dinamis, gunakan {{ $title ?? 'Default Title' }} --}}
    <title>{{ $title ?? 'Voltix Admin' }}</title>

    {{-- Fonts & Styles --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="h-full font-sans">
    <div class="min-h-full">
        {{-- Navbar --}}
        <nav class="bg-white border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <h1 class="text-indigo-600 font-bold text-xl">Voltix Web Admin</h1>
                        <div class="hidden md:block ml-10">
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 font-medium px-3 py-2 rounded-md hover:bg-gray-100">Dashboard</a>
                            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }} font-medium px-3 py-2 rounded-md text-sm">Users</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                         <span class="font-medium text-sm text-gray-600 hidden sm:block">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Sign out" class="text-gray-500 hover:text-indigo-600">
                                 <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Header Halaman --}}
        @if (isset($header))
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold leading-6 text-gray-900">
                    {{ $header }}
                </h1>
            </div>
        </header>
        @endif

        {{-- Konten Utama --}}
        <main>
            <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    {{-- Slot untuk script khusus halaman --}}
    @stack('scripts')
</body>
</html>