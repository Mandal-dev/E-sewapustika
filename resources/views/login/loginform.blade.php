@extends('login.login')


@section('data')
    <div class="d-flex align-items-center div_main justify-content-center w-100 w-md-50 bg-gradient p-4 magin-top: 50%">
        <!-- White Card -->
        <div class="bg-white rounded-4 shadow-lg position-relative p-5" style="width: 600px; height: 400px;">

            <!-- Logo -->
            <div class="position-absolute start-50 translate-middle-x" style="top: -65px;">
                <img src="{{ asset('img/logo.png') }}" alt="E-Police Logo" width="130" height="110">
            </div>

            <!-- Inner Content -->
            <div class="pt-5 mt-4">
                <h2 class="fw-bold text-center text-dark mb-2">Login to your account</h2>
                <p class="text-center text-muted mb-4">Enter your mobile number.</p>

                <!-- Flash Messages -->
                @if ($errors->any())
                    <div class="alert alert-danger py-2 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success py-2 text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.user') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="mobile" value="{{ old('mobile') }}" placeholder="Mobile number"
                            class="form-control form-control-lg" required>
                        @error('mobile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-lg w-100 text-white fw-semibold"
                        style="background-color:#ff7b00;">
                        Login
                    </button>
                </form>

                <!-- Footer -->
                <p class="mt-4 text-center text-muted">
                    Don’t have an account?
                    <a href="#" class="fw-semibold text-decoration-none" style="color:#ff7b00;">Sign up</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Custom Gradient Background -->
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #ff9f43, #ffecd2);
        }
    </style>
@endsection
