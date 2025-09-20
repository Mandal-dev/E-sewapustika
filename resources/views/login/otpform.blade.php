@extends('login.login')

@section('data')
<div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-100 p-6">
    <div class="bg-white rounded-2xl shadow-lg w-[600px] h-[470px] p-8 relative">

        <!-- Logo -->
        <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
            <img src="{{ asset('img/logo.png') }}" alt="E-Police Logo" class="w-[150px] h-[130px]">
        </div>

        <div class="pt-16 px-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Enter OTP</h2>
            <p class="text-center text-gray-500 mb-6">We’ve sent a 6-digit code to your registered mobile.</p>

            <!-- Flash Errors -->
            @if ($errors->any())
                <div class="text-red-500 text-center mb-4">{{ $errors->first() }}</div>
            @endif

            <!-- Success Message -->
            @if (session('success'))
                <div class="text-green-500 text-center mb-4">{{ session('success') }}</div>
            @endif

            <!-- OTP Form -->
            <form action="{{ route('login.verifyOtp') }}" method="POST">
                @csrf
                <div class="flex justify-center gap-3 mb-4">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" name="otp[]" maxlength="1"
                            class="otp-input w-12 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            aria-label="OTP digit {{ $i + 1 }}" required>
                    @endfor
                </div>

                @error('otp')
                    <p class="text-red-500 text-sm text-center">{{ $message }}</p>
                @enderror

                @if(Session::has('otp'))
                    <p class="text-gray-500 text-sm text-center"><strong>Test OTP:</strong> {{ Session::get('otp') }}</p>
                @endif

                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition">
                    Verify
                </button>
            </form>

            <!-- Resend OTP -->
            <p class="mt-6 text-center text-gray-600">
                Didn’t receive OTP?
                <button id="resend-btn" class="text-orange-500 font-semibold hover:underline" disabled>
                    Resend (<span id="timer">30</span>s)
                </button>
            </p>

            <!-- Hidden form for Resend OTP -->
            <form id="resend-otp-form" action="{{ route('otp.resend') }}" method="POST" style="display:none;">
                @csrf
            </form>

        </div>
    </div>
</div>

<script>
    // ========================
    // Auto-move OTP input
    // ========================
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

    // ========================
    // Timer and Resend OTP
    // ========================
    let timeLeft = 30; // 30 seconds
    const timerEl = document.getElementById('timer');
    const resendBtn = document.getElementById('resend-btn');
    const resendForm = document.getElementById('resend-otp-form');

    const startTimer = () => {
        resendBtn.disabled = true;
        timerEl.textContent = timeLeft;

        const countdown = setInterval(() => {
            timeLeft--;
            timerEl.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                resendBtn.disabled = false;
                resendBtn.textContent = "Resend OTP";
            }
        }, 1000);
    };

    startTimer();

    // ========================
    // Resend OTP button click
    // ========================
    resendBtn.addEventListener('click', function() {
        // Submit hidden form
        resendForm.submit();

        // Reset timer for next OTP
        timeLeft = 30;
        resendBtn.textContent = "Resend (" + timeLeft + "s)";
        startTimer();
    });
</script>
@endsection
