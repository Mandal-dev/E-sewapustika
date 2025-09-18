<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginUserController extends Controller
{
    // ===============================
    // Show login page
    // ===============================
    public function showLoginPage()
    {
        // If user session exists → redirect to dashboard
        if (Session::has('user')) {
            return redirect()->route('dashboard');
        }

        return view('login.loginform'); // login form blade
    }

    // ===============================
    // Handle login (send OTP)
    // ===============================
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10', // adjust if needed
        ]);

        $mobile = $request->input('mobile');

        $user = DB::table('police_users')
            ->where('mobile', $mobile)
            ->first();

        if ($user) {
            // Generate OTP
            $otp = rand(100000, 999999);
            Session::put('otp', $otp);
            Session::put('otp_mobile', $mobile);

            // (Integrate SMS sending here if required)

            // ✅ redirect to OTP page route instead of returning view directly
            return redirect()->route('otp.page')->with('success', 'OTP sent to your mobile.');
        }

        return back()->withErrors([
            'mobile' => 'Invalid credentials.',
        ]);
    }

    // ===============================
    // Show OTP form page
    // ===============================
    public function showOtpPage()
    {
        // If no OTP session, force re-login
        if (!Session::has('otp_mobile')) {
            return redirect()->route('login.page')
                ->withErrors(['mobile' => 'Session expired. Please login again.']);
        }

        return view('login.otpform'); // otp form blade
    }

    // ===============================
    // Verify OTP
    // ===============================
   public function verifyOtp(Request $request)
{
    // If otp[] array exists (like input boxes), merge into one string
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
            ->select('id', 'district_id', 'designation_type', 'police_name', 'post')
            ->where('mobile', $mobile)
            ->first();

        if ($user) {
            // ✅ Fetch district name after we know district_id
            $district_name = DB::table('districts')
                ->where('id', $user->district_id)
                ->value('district_name');

            // Clear OTP session
            Session::forget(['otp', 'otp_mobile']);

            // Store user session
            Session::put('user', [
                'id'               => $user->id,
                'district_id'      => $user->district_id,
                'designation_type' => $user->designation_type,
                'post'             => $user->post,
                'district_name'    => $district_name, // ✅ correct value
                'name'             => $user->police_name,
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Login successful! Welcome, ' . $user->police_name);
        }

        return redirect()->route('login.page')
            ->withErrors(['otp' => 'User not found or inactive.']);
    }

    // ✅ Wrong OTP → redirect to OTP page
    return redirect()->route('otp.page')
        ->withErrors(['otp' => 'Invalid OTP.']);
}


    // ===============================
    // Logout function
    // ===============================
    public function logout()
    {
        Session::forget('user'); // Clear user session
        return redirect()->route('login.page')
            ->with('success', 'You have been logged out successfully.');
    }
}
