@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 to-blue-700">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-900">Sekretariat Gereja</h1>
            <p class="text-gray-500 mt-2">Sistem Informasi Admin</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required 
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                Masuk
            </button>
        </form>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg text-sm text-gray-600">
            <p class="font-semibold mb-2">Demo Credentials:</p>
            <p>Email: admin@gereja.com</p>
            <p>Password: password</p>
        </div>
    </div>
</div>
@endsection
