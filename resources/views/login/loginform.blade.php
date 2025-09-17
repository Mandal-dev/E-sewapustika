
@extends('login.login')

@section('data')
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-orange-400 to-orange-100 p-6">

        <!-- White Card -->
        <div class="bg-white rounded-2xl shadow-lg w-[600px] h-[400px] p-8 relative">

            <!-- Logo (half inside, half outside) -->
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                <img src="{{ asset('img/logo.png') }}" alt="E-Police Logo" class="w-[150px] h-[130px]">
            </div>

            <!-- Inner Content -->
            <div class="pt-16 px-6">
                <!-- Title -->
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Login to your account</h2>
                <p class="text-center text-gray-500 mb-6">Enter your mobile number.</p>

                <!-- Form -->
                <form action="{{ route('login.user') }}" method="POST">
                    @csrf
                    <input type="text" name="mobile" placeholder="Mobile number"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">

                    @error('mobile')
                        <small class="text-red-500">{{ $message }}</small>
                    @enderror

                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition mt-4">
                        Login
                    </button>
                </form>

                <!-- Footer -->
                <p class="mt-6 text-center text-gray-600">
                    Don’t have an account?
                    <a href="#" class="text-orange-500 font-semibold hover:underline">Sign up</a>
                </p>
            </div>
        </div>
    </div>
@endsection
