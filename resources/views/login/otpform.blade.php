@extends('login.login')

@section('data')
<div class="d-flex align-items-center justify-content-center w-100 w-md-50 bg-gradient p-4">
    <!-- White Card -->
    <div class="bg-white rounded-4 shadow-lg position-relative p-5" style="width: 600px; height: 470px;">

        <!-- Logo -->
        <div class="position-absolute start-50 translate-middle-x" style="top: -65px;">
            <img src="{{ asset('img/logo.png') }}" alt="E-Police Logo" width="130" height="110">
        </div>

        <div class="pt-5 mt-4">
            <h2 class="fw-bold text-center text-dark mb-2">Enter OTP</h2>
            <p class="text-center text-muted mb-4">We’ve sent a 6-digit code to your registered mobile.</p>

            <!-- Flash Errors -->
            @if ($errors->any())
                <div class="alert alert-danger py-2 text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success py-2 text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- OTP Form -->
            <form action="{{ route('login.verifyOtp') }}" method="POST">
                @csrf
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" name="otp[]" maxlength="1"
                            class="form-control text-center fs-4 fw-semibold otp-input"
                            style="width: 50px; height: 55px; border-radius: 8px;" required
                            aria-label="OTP digit {{ $i + 1 }}">
                    @endfor
                </div>

                @error('otp')
                    <p class="text-danger text-center small">{{ $message }}</p>
                @enderror

                @if(Session::has('otp'))
                    <p class="text-muted text-center small"><strong>Test OTP:</strong> {{ Session::get('otp') }}</p>
                @endif

                <button type="submit" class="btn w-100 text-white fw-semibold py-2" style="background-color:#ff7b00;">
                    Verify
                </button>
            </form>

            <!-- Resend OTP -->
            <p class="mt-4 text-center text-muted">
                Didn’t receive OTP?
                <button id="resend-btn" class="btn btn-link p-0 text-decoration-none fw-semibold"
                    style="color:#ff7b00;" disabled>
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

<!-- Custom Gradient -->
<style>
    .bg-gradient {
        background: linear-gradient(135deg, #ff9f43, #ffecd2);
    }
</style>

<!-- OTP Input + Timer Script -->
<script>
    // Auto-move OTP input focus
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

    // Timer and Resend
    let timeLeft = 30;
    const timerEl = document.getElementById('timer');
    const resendBtn = document.getElementById('resend-btn');
    const resendForm = document.getElementById('resend-otp-form');

    const startTimer = () => {
        resendBtn.disabled = true;
        timerEl.textContent = timeLeft;
        resendBtn.innerHTML = `Resend (<span id="timer">${timeLeft}</span>s)`;

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

    resendBtn.addEventListener('click', function () {
        resendForm.submit();
        timeLeft = 30;
        startTimer();
    });
</script>
@endsection
