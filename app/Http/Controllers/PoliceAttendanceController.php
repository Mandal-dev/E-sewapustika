<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoliceAttendanceController extends Controller
{
    // Display all attendance records using Query Builder
        // Show attendance page

    // Fetch attendance table based on role
    public function index()
    {
        try {
            $user = Session::get('user');
            if (!$user) return redirect('/');

            $userId = $user['id'];
            $attendance = collect();

            // Role-based access
            if ($user['designation_type'] === 'Police') {
                return redirect()->back()->with('error', 'Access denied.');
            } elseif ($user['designation_type'] === 'Station_Head') {
                // All police under the same station as this station head
                $myStationId = DB::table('police_users')->where('id', $userId)->value('police_station_id');

                $attendance = DB::table('police_attendance AS pa')
                    ->join('police_users AS u', 'pa.police_user_id', '=', 'u.id')
                    ->leftJoin('police_stations AS s', 'pa.police_station_id', '=', 's.id')
                    ->where('u.police_station_id', $myStationId)
                    ->select('pa.*', 'u.police_name', 's.name AS station_name')
                    ->orderBy('pa.attendance_date', 'desc')
                    ->get();

            } elseif ($user['designation_type'] === 'Head_Person') {
                // All police in the same district
                $attendance = DB::table('police_attendance AS pa')
                    ->join('police_users AS u', 'pa.police_user_id', '=', 'u.id')
                    ->leftJoin('police_stations AS s', 'pa.police_station_id', '=', 's.id')
                    ->where('u.district_id', $user['district_id'])
                    ->select('pa.*', 'u.police_name', 's.name AS station_name')
                    ->orderBy('pa.attendance_date', 'desc')
                    ->get();

            } elseif ($user['designation_type'] === 'Admin') {
                // All attendance system-wide
                $attendance = DB::table('police_attendance AS pa')
                    ->join('police_users AS u', 'pa.police_user_id', '=', 'u.id')
                    ->leftJoin('police_stations AS s', 'pa.police_station_id', '=', 's.id')
                    ->select('pa.*', 'u.police_name', 's.name AS station_name')
                    ->orderBy('pa.attendance_date', 'desc')
                    ->get();
            }

            return view('attendance.table', compact('attendance'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
    // Show form to create attendance
    public function create()
    {
        $policeUsers = DB::table('police_users')->where('is_active', 1)->get();
        $stations = DB::table('police_stations')->get();
        $districts = DB::table('districts')->get();

        return view('attendance.create', compact('policeUsers', 'stations', 'districts'));
    }

    // Store attendance record using Query Builder
    public function store(Request $request)
    {
        $request->validate([
            'police_user_id' => 'required|exists:police_users,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Leave,On Duty',
            'police_station_id' => 'nullable|exists:police_stations,id',
            'district_id' => 'nullable|exists:districts,id',
            'taluka' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'shift' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        DB::table('police_attendance')->insert([
            'police_user_id' => $request->police_user_id,
            'police_station_id' => $request->police_station_id,
            'district_id' => $request->district_id,
            'taluka' => $request->taluka,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'shift' => $request->shift,
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }
        // Show calendar with check-in

    // User clicks "Check-in"
    public function checkIn(Request $request)
    {
        $user = Session::get('user');

        if(!$user){
            return response()->json(['status' => 'error', 'message' => 'User not logged in']);
        }

        // You can record check-in time in session or database
        Session::put('check_in', now());

        return response()->json(['status' => 'success', 'message' => 'Checked in successfully']);
    }

    // Mark attendance for a specific date
    public function markAttendance(Request $request)
    {
        $request->validate([
            'attendance_date' => 'required|date',
        ]);

        $user = Session::get('user');

        if(!Session::has('check_in')){
            return response()->json(['status' => 'error', 'message' => 'Please check-in first']);
        }

        // Insert or update attendance record
        DB::table('police_attendance')->updateOrInsert(
            ['police_user_id' => $user['id'], 'attendance_date' => $request->attendance_date],
            [
                'status' => 'Present',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Attendance marked']);
    }
    public function calendar()
{
    $user = Session::get('user');

    return view('attendance.calendar', compact('user'));
}

// Fetch attendance data for calendar
public function getAttendanceEvents(Request $request)
{
    $user = Session::get('user');

    $month = $request->get('month'); // 'YYYY-MM'
    $start = $month . '-01';
    $end = date("Y-m-t", strtotime($start));

    $attendances = DB::table('police_attendance')
        ->where('police_user_id', $user['id'])
        ->whereBetween('attendance_date', [$start, $end])
        ->get();

    $events = [];

    foreach($attendances as $att) {
        if($att->status == 'Present'){
            $events[] = [
                'title' => 'Present',
                'start' => $att->attendance_date,
                'color' => 'green',
            ];
        } else {
            $events[] = [
                'title' => $att->status,
                'start' => $att->attendance_date,
                'color' => 'red',
            ];
        }
    }

    return response()->json($events);
}

public function checkout(Request $request)
{
    $userId = auth()->id();
    $today = now()->toDateString();

    $attendance = DB::table('police_attendance')
        ->where('police_user_id', $userId)
        ->where('attendance_date', $today)
        ->first();

    if (!$attendance || !$attendance->checkin_time) {
        return response()->json([
            'status' => 'error',
            'message' => 'You have not checked in today!'
        ]);
    }

    if ($attendance->checkout_time) {
        return response()->json([
            'status' => 'error',
            'message' => 'You already checked out today!'
        ]);
    }

    DB::table('police_attendance')
        ->where('id', $attendance->id)
        ->update([
            'checkout_time' => now()->format('H:i:s'),
            'updated_at' => now(),
        ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Checked out successfully at ' . now()->format('H:i:s'),
    ]);
}

}
