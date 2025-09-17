
@extends('login.login')

@section('data')
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-100 p-6">

        <!-- White Card -->
        <div class="bg-white rounded-2xl shadow-lg w-[600px] h-[420px] p-8 relative">

            <!-- Logo (half inside, half outside) -->
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                <img src="{{ asset('img/logo.png') }}" alt="E-Police Logo" class="w-[150px] h-[130px]">
            </div>

         <!-- Inner content -->
            <div class="pt-12 text-center">
                <!-- Title -->
                <br>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Verification</h2>
                <p class="text-gray-500 mb-6">Enter OTP sent on your mobile number</p>

                <form action="{{ route('login.verifyOtp') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- OTP Inputs -->
                    <div class="flex justify-center gap-3">
                        <input type="text" name="otp[]" maxlength="1"
                            class="w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            aria-label="OTP digit 1" required>
                        <input type="text" name="otp[]" maxlength="1"
                            class="w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            aria-label="OTP digit 2" required>
                        <input type="text" name="otp[]" maxlength="1"
                            class="w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            aria-label="OTP digit 3" required>
                        <input type="text" name="otp[]" maxlength="1"
                            class="w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            aria-label="OTP digit 4" required>
                        <input type="text" name="otp[]" maxlength="1"
                            class="w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            aria-label="OTP digit 5" required>
                        <input type="text" name="otp[]" maxlength="1"
                            class="w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            aria-label="OTP digit 6" required>
                    </div>

                    <!-- Show validation error -->
                    @error('otp')
                        <p class="text-red-500 text-sm text-center">{{ $message }}</p>
                    @enderror

                    <!-- Show test OTP (for demo only) -->
                    @if(Session::has('otp'))
                        <p class="text-gray-500 text-sm text-center">
                            <strong>Test OTP:</strong> {{ Session::get('otp') }}
                        </p>
                    @endif

                    <!-- Submit button -->
                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition">
                        Verify
                    </button>
                </form>

                <!-- Resend -->
                <p class="mt-4 text-gray-600">
                    Didn’t get OTP?
                    <a href="#" class="text-orange-500 font-semibold hover:underline">Resend</a>
                </p>
            </div>
        </div>
    </div>
       <!-- Auto move OTP script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inputs = document.querySelectorAll('input[name="otp[]"]');
            inputs.forEach((input, index) => {
                input.addEventListener("input", () => {
                    if (input.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });
                input.addEventListener("keydown", (e) => {
                    if (e.key === "Backspace" && !input.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });
        });
    </script>
@endsection
