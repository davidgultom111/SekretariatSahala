<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistem Informasi Sekretariat Gereja</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @if (auth()->check())
        <div class="flex h-screen">
            <!-- Sidebar -->
            <aside class="w-64 bg-blue-900 text-white shadow-lg">
                <div class="p-6 border-b border-blue-800">
                    <h1 class="text-2xl font-bold">Sekretariat</h1>
                    <p class="text-sm text-blue-200">GPdI Sahala Palembang</p>
                </div>

                <nav class="p-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ Route::is('dashboard') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                            <path d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                            <path d="M14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('member.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ Route::is('member.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                        </svg>
                        <span>Data Diri Jemaat</span>
                    </a>

                    <a href="{{ route('letter.types') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ Route::is('letter.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.657 6.243A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                        </svg>
                        <span>Surat-Menyurat</span>
                    </a>

                    <a href="{{ route('letter.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ Route::is('letter.index') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path>
                        </svg>
                        <span>Arsip Surat</span>
                    </a>

                    <div class="pt-6 border-t border-blue-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg transition hover:bg-red-700">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-auto">
                <div class="p-8">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    @else
        @yield('content')
    @endif
</body>
</html>
