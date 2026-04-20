<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali!</h2>
        <p class="text-sm text-gray-500 mt-1">Silakan masuk ke akun Anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />
        <title>Login</title>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="relative">
            <x-input-label for="email" :value="__('Email')" />

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>

                <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <x-text-input id="password" class="block w-full pl-10"
                    type="password"
                    name="password"
                    required autocomplete="current-password"
                    placeholder="••••••••" />
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 text-base">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        {{-- Jika Anda ingin menambahkan link ke register --}}
        {{--
        <div class="mt-4 text-center">
            <span class="text-sm text-gray-600">Belum punya akun? </span>
            <a href="{{ route('register') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">Daftar sekarang</a>
        </div>
        --}}
    </form>
</x-guest-layout>