<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginUserController extends Controller
{
    // ===============================
    // Show login page
    // ===============================
    public function showLoginPage()
    {
        if (Session::has('user')) {
            return redirect()->route('dashboard');
        }

        return view('login.loginform');
    }

    // ===============================
    // Handle login (send OTP)
    // ===============================
public function login(Request $request)
{
    $request->validate([
        'mobile' => 'required|digits:10',
    ]);

    $mobile = $request->input('mobile');

    $user = DB::table('police_users')->where('mobile', $mobile)->first();
    if (!$user) {
        return back()->withErrors(['mobile' => 'Invalid credentials.']);
    }

    // -------------------
    // Generate OTP and store in session with expiry and attempts
    // -------------------
    $otp = rand(100000, 999999);
    Session::put('otp', $otp);
    Session::put('otp_mobile', $mobile);
    Session::put('otp_expires_at', now()->addMinutes(5)); // OTP valid for 5 minutes
    Session::put('otp_attempts', 0); // track wrong attempts

    // -------------------
    // Prepare SMS message
    // -------------------
    $message = "{$otp} is your verification code. Use it to complete your login. Do not share it with anyone. HBGADGET";

    // -------------------
    // SMS API credentials
    // -------------------
    $api_url   = "https://api.pinnacle.in/index.php/sms/urlsms";
    $apikey    = "10bdfb-cee7f2-f322fa-108c09-3e3a7b";
    $senderid  = "HBTSPL";
    $dlttempid = "1707175828703777137";

    try {
        // Send SMS using query parameters
        $response = Http::timeout(15)
            ->withoutVerifying() // bypass SSL for localhost
            ->get($api_url, [
                'sender'     => $senderid,
                'numbers'    => $mobile,
                'messagetype'=> 'TXT',
                'message'    => $message,
                'response'   => 'Y',
                'apikey'     => $apikey,
                'dlttempid'  => $dlttempid
            ]);

        $result = $response->json();

        // -------------------
        // Log SMS status (without exposing OTP)
        // -------------------
        Log::info('SMS sent', [
            'mobile'   => $mobile,
            'status'   => $result['status'] ?? 'unknown',
            'uniqueid' => $result['data'][0]['uniqueid'] ?? null,
            'ip'       => $request->ip(), // store device IP
        ]);

        // -------------------
        // Save SMS log in DB
        // -------------------
        $insertData = [
            'mobile_no'  => $mobile,
            'shop_id'    => $request->ip(), // store IP instead of shop_id
            'status'     => $result['status'] === 'success' ? 'success' : 'failed',
            'unique_id'  => $result['data'][0]['uniqueid'] ?? null,
            'date'       => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('sms_logs')->insert($insertData);

        if ($insertData['status'] !== 'success') {
            return back()->withErrors(['mobile' => 'SMS sending failed. Please try again.']);
        }

    } catch (\Exception $e) {
        Log::error('SMS API Exception: ' . $e->getMessage());
        return back()->withErrors(['mobile' => 'SMS sending failed. Please try again later.']);
    }

    return redirect()->route('otp.page')->with('success', 'OTP sent to your mobile.');
}


    // ===============================
    // Show OTP form page
    // ===============================
    public function showOtpPage()
    {
        if (!Session::has('otp_mobile')) {
            return redirect()->route('login.page')
                ->withErrors(['mobile' => 'Session expired. Please login again.']);
        }

        return view('login.otpform');
    }

    // ===============================
    // Verify OTP
    // ===============================
    public function verifyOtp(Request $request)
    {
        if (is_array($request->otp)) {
            $otp = implode('', $request->otp);
            $request->merge(['otp' => $otp]);
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $sessionOtp = Session::get('otp');
        $mobile     = Session::get('otp_mobile');
        $expiry     = Session::get('otp_expires_at');
        $attempts   = Session::get('otp_attempts', 0);

        if (!$sessionOtp || !$mobile) {
            return redirect()->route('login.page')->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        if (now()->greaterThan($expiry)) {
            Session::forget(['otp','otp_mobile','otp_expires_at','otp_attempts']);
            return redirect()->route('login.page')->withErrors(['otp' => 'OTP expired. Please request a new one.']);
        }

        if ($attempts >= 5) {
            Session::forget(['otp','otp_mobile','otp_expires_at','otp_attempts']);
            return redirect()->route('login.page')->withErrors(['otp' => 'Maximum attempts exceeded. Please request a new OTP.']);
        }

        if ($request->otp != $sessionOtp) {
            Session::increment('otp_attempts');
            return redirect()->route('otp.page')->withErrors(['otp' => 'Invalid OTP.']);
        }

        $user = DB::table('police_users')
            ->select('id','district_id','designation_type','police_name','post')
            ->where('mobile', $mobile)
            ->first();

        if (!$user) {
            return redirect()->route('login.page')->withErrors(['otp' => 'User not found or inactive.']);
        }

        $district_name = DB::table('districts')
            ->where('id', $user->district_id)
            ->value('district_name');

        Session::forget(['otp','otp_mobile','otp_expires_at','otp_attempts']);

        Session::put('user', [
            'id'               => $user->id,
            'district_id'      => $user->district_id,
            'designation_type' => $user->designation_type,
            'post'             => $user->post,
            'district_name'    => $district_name,
            'name'             => $user->police_name,
        ]);

        return redirect()->route('dashboard')->with('success', 'Login successful! Welcome, ' . $user->police_name);
    }

    // ===============================
    // Logout
    // ===============================
    public function logout()
    {
        Session::forget('user');
        return redirect()->route('login.page')->with('success', 'You have been logged out successfully.');
    }

   // ===============================
// Resend OTP
// ===============================
public function resendOtp(Request $request)
{
    $mobile = Session::get('otp_mobile');

    if (!$mobile) {
        return redirect()->route('login.page')
            ->withErrors(['mobile' => 'Session expired. Please login again.']);
    }

    // -------------------
    // Delete previous OTP session
    // -------------------
    Session::forget(['otp', 'otp_expires_at']);

    // -------------------
    // Generate new OTP
    // -------------------
    $otp = rand(100000, 999999);

    // Store new OTP and expiry (5 min)
    Session::put('otp', $otp);
    Session::put('otp_mobile', $mobile);
    Session::put('otp_expires_at', now()->addMinutes(5));

    // -------------------
    // Prepare SMS
    // -------------------
    $message = "{$otp} is your verification code. Use it to complete your login. Do not share it with anyone. HBGADGET";

    $api_url   = "https://api.pinnacle.in/index.php/sms/urlsms";
    $apikey    = "10bdfb-cee7f2-f322fa-108c09-3e3a7b";
    $senderid  = "HBTSPL";
    $dlttempid = "1707175828703777137";

    $full_url = "{$api_url}?sender=" . urlencode($senderid) .
        "&numbers=" . urlencode($mobile) .
        "&messagetype=" . urlencode("TXT") .
        "&message=" . urlencode($message) .
        "&response=Y" .
        "&apikey=" . urlencode($apikey) .
        "&dlttempid=" . urlencode($dlttempid);

    try {
        $response = Http::timeout(15)->withoutVerifying()->get($full_url);
        $result = $response->json();
        Log::info('Resend OTP SMS Response:', $result);

        // Optionally, log SMS in database (like sms_logs)
        DB::table('sms_logs')->insert([
            'mobile_no' => $mobile,
            'shop_id'   => request()->ip(), // store device IP instead of shop
            'status'    => $result['status'] ?? 'failed',
            'unique_id' => $result['data'][0]['uniqueid'] ?? null,
            'date'      => now(),
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

    } catch (\Exception $e) {
        Log::error('Resend OTP SMS Exception: ' . $e->getMessage());
    }

    return redirect()->route('otp.page')
        ->with('success', 'A new OTP has been sent to your mobile.');
}


}
