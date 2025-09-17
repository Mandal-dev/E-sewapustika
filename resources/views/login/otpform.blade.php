@extends('login.login')

@section('data')
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-100 p-6">
        <!-- White Card -->
        <div class="bg-white rounded-2xl shadow-lg w-[600px] h-[420px] p-8 relative">

            <!-- Logo -->
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                <img src="{{ asset('img/logo.png') }}" alt="E-Police Logo" class="w-[150px] h-[130px]">
            </div>

            <!-- Inner Content -->
            <div class="pt-16 px-6">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Enter OTP</h2>
                <p class="text-center text-gray-500 mb-6">We’ve sent a 6-digit code to your registered mobile.</p>

                <!-- Flash Errors -->
                @if ($errors->any())
                    <div class="text-red-500 text-center mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Success Message -->
                @if (session('success'))
                    <div class="text-green-500 text-center mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- OTP Form -->
                <form action="{{ route('login.verifyOtp') }}" method="POST">
                    @csrf

                    <!-- OTP Inputs -->
                    <div class="flex justify-center gap-3 mb-4">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" name="otp[]" maxlength="1"
                                class="otp-input w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                                aria-label="OTP digit {{ $i + 1 }}" required>
                        @endfor
                    </div>

                    <!-- Validation Error -->
                    @error('otp')
                        <p class="text-red-500 text-sm text-center">{{ $message }}</p>
                    @enderror

                    <!-- Show Test OTP (ONLY for testing) -->
                    @if(Session::has('otp'))
                        <p class="text-gray-500 text-sm text-center">
                            <strong>Test OTP:</strong> {{ Session::get('otp') }}
                        </p>
                    @endif

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition">
                        Verify
                    </button>
                </form>

                <!-- Resend Link -->
                <p class="mt-6 text-center text-gray-600">
                    Didn’t receive OTP?
                    <a href="{{ route('login.page') }}" class="text-orange-500 font-semibold hover:underline">Resend</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Auto-move OTP input -->
    <script>
        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
            input.addEventListener('input', function () {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === "Backspace" && this.value === "" && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
@endsection
