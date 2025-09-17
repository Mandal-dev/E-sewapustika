<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginUserController extends Controller
{
    // Show login page
    public function showLoginPage()
    {
        // If user session exists → redirect to dashboard
        if (Session::has('user')) {
            return redirect()->route('dashboard');
        }

        return view('login.loginform'); // your Blade file
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10', // adjust digits as needed
        ]);

        $mobile = $request->input('mobile');

        $user = DB::table('police_users')
            ->where('mobile', $mobile)
            ->first();

        if ($user) {
            $otp = rand(100000, 999999);
            Session::put('otp', $otp);
            Session::put('otp_mobile', $mobile);

            // Here you can integrate SMS sending if needed

            return view('login.otpform')->with('success', 'OTP sent to your mobile.');
        }

        return back()->withErrors([
            'mobile' => 'Invalid credentials.',
        ]);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        // If otp[] (array) exists → combine into string
        if (is_array($request->otp)) {
            $otp = implode('', $request->otp);
            $request->merge(['otp' => $otp]);
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $sessionOtp = Session::get('otp');
        $mobile     = Session::get('otp_mobile');

        if (!$sessionOtp || !$mobile) {
            return redirect()->route('login.page')
                ->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        if ($request->otp == $sessionOtp) {
            $user = DB::table('police_users')
                ->select('id', 'district_id', 'designation_type', 'police_name')
                ->where('mobile', $mobile)
                ->first();

            if ($user) {
                // Clear OTP session
                Session::forget(['otp', 'otp_mobile']);

                // Store user session
                Session::put('user', [
                    'id'               => $user->id,
                    'district_id'      => $user->district_id,
                    'designation_type' => $user->designation_type,
                    'name'             => $user->police_name,
                ]);

                return redirect()->route('dashboard')
                    ->with('success', 'Login successful! Welcome, ' . $user->police_name);
            }

            return redirect()->route('login.page')
                ->withErrors(['otp' => 'User not found or inactive.']);
        }

        return back()->withErrors(['otp' => 'Invalid OTP.']);
    }

    // Logout function
    public function logout()
    {
        Session::forget('user'); // Clear user session
        return redirect()->route('login.page')
            ->with('success', 'You have been logged out successfully.');
    }
}
