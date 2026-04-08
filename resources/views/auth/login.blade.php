<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-[#121212]">Đăng nhập hệ thống KTX</h1>
        <p class="mt-1 text-sm text-[#606060]">Vui lòng đăng nhập để sử dụng các chức năng quản lý.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mật khẩu" />
            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-200/80 text-black focus:ring-black/10"
                    name="remember"
                >
                <span class="ms-2 text-sm text-[#606060]">Nhớ đăng nhập</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="text-sm text-[#606060] hover:text-[#121212] hover:underline focus:outline-none focus:ring-2 focus:ring-black/10 focus:ring-offset-2 rounded-md"
                    href="{{ route('password.request') }}"
                >
                    Quên mật khẩu?
                </a>
            @endif
        </div>

        <x-primary-button class="mt-2 w-full">
            Đăng nhập
        </x-primary-button>
    </form>
</x-guest-layout>
