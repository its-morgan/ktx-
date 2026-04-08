<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-[#121212]">Đăng ký tài khoản sinh viên</h1>
        <p class="mt-1 text-sm text-[#606060]">Điền đầy đủ thông tin để tạo tài khoản mới.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Họ và tên" />
            <x-text-input
                id="name"
                class="mt-1 block w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
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
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" />
            <x-text-input
                id="password_confirmation"
                class="mt-1 block w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="gioitinh" value="Giới tính" />
            <select id="gioitinh" name="gioitinh" class="linear-select mt-1" required>
                <option value="">-- Chọn giới tính --</option>
                <option value="Nam" {{ old('gioitinh') === 'Nam' ? 'selected' : '' }}>Nam</option>
                <option value="Nữ" {{ old('gioitinh') === 'Nữ' ? 'selected' : '' }}>Nữ</option>
            </select>
            <x-input-error :messages="$errors->get('gioitinh')" class="mt-2" />
        </div>

        <x-primary-button class="mt-2 w-full">
            Đăng ký
        </x-primary-button>

        <p class="text-center text-sm text-[#606060]">
            Đã có tài khoản?
            <a class="font-medium text-[#121212] hover:underline" href="{{ route('login') }}">
                Đăng nhập
            </a>
        </p>
    </form>
</x-guest-layout>
