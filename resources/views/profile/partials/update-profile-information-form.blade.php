<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if($user->vaitro === 'sinhvien')
            {{-- Thông tin bổ sung cho sinh viên --}}
            <div>
                <x-input-label for="masinhvien" value="Mã sinh viên" />
                <x-text-input id="masinhvien" name="masinhvien" type="text" class="mt-1 block w-full" :value="old('masinhvien', optional($user->sinhvien)->masinhvien)" />
                <x-input-error class="mt-2" :messages="$errors->get('masinhvien')" />
            </div>

            <div>
                <x-input-label for="lop" value="Lớp" />
                <x-text-input id="lop" name="lop" type="text" class="mt-1 block w-full" :value="old('lop', optional($user->sinhvien)->lop)" />
                <x-input-error class="mt-2" :messages="$errors->get('lop')" />
            </div>

            <div>
                <x-input-label for="sodienthoai" value="Số điện thoại" />
                <x-text-input id="sodienthoai" name="sodienthoai" type="text" class="mt-1 block w-full" :value="old('sodienthoai', optional($user->sinhvien)->sodienthoai)" />
                <x-input-error class="mt-2" :messages="$errors->get('sodienthoai')" />
            </div>

            <div>
                <x-input-label for="gioitinh" value="Giới tính" />
                <select id="gioitinh" name="gioitinh" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" {{ old('gioitinh', $user->gioitinh ?? '') === 'Nam' ? 'selected' : '' }}>Nam</option>
                    <option value="Nữ" {{ old('gioitinh', $user->gioitinh ?? '') === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gioitinh')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
