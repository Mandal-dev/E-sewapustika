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
            return view('attendance.create', ['userId' => $userId]);
        }

        // Base query for attendance data
        $query = DB::table('police_attendance AS pa')
            ->join('police_users AS u', 'pa.police_user_id', '=', 'u.id')
            ->leftJoin('police_stations AS s', 'pa.police_station_id', '=', 's.id')
            ->select(
                'u.id',
                'pa.police_user_id',
                'pa.police_station_id',
                'pa.attendance_date',
                'pa.status',
                'u.police_name',
                'u.designation_type',
                's.name AS station_name'
            )

            ->orderBy('pa.attendance_date', 'desc');

        if ($user['designation_type'] === 'Station_Head') {
            // All police under same station
            $myStationId = DB::table('police_users')->where('id', $userId)->value('police_station_id');
            $attendance = $query->where('u.police_station_id', $myStationId)->get();
        } elseif ($user['designation_type'] === 'Head_Person') {
            // All police in the same district
            $attendance = $query->where('u.district_id', $user['district_id'])->get();
        } elseif ($user['designation_type'] === 'Admin') {
            // All attendance system-wide
            $attendance = $query->get();
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

    public function checkIn(Request $request)
    {
        $user = Session::get('user');

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not logged in']);
        }

        $today = date('Y-m-d');

        // Check if attendance is already marked for today
        $existingAttendance = DB::table('police_attendance')
            ->where('police_user_id', $user['id'])
            ->where('attendance_date', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance for today is already marked'
            ]);
        }

        // Fetch district_id and police_station_id for this user
        $policeData = DB::table('police_users')
            ->where('id', $user['id'])
            ->select('district_id', 'police_station_id')
            ->first();

        if (!$policeData || !$policeData->district_id || !$policeData->police_station_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'District or Police Station data missing for this user'
            ]);
        }

        // Insert attendance record for today
        DB::table('police_attendance')->insert([
            'police_user_id'    => $user['id'],
            'police_station_id' => $policeData->police_station_id,
            'district_id'       => $policeData->district_id,
            'attendance_date'   => $today,
            'status'            => 'Present', // default on check-in
            'checkin_time'      => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Store check-in time in session if needed
        Session::put('check_in', now());

        return response()->json([
            'status' => 'success',
            'message' => 'Checked in successfully'
        ]);
    }

    // Mark attendance for a specific date
    public function markAttendance(Request $request)
    {
        $request->validate([
            'attendance_date' => 'required|date',
        ]);

        $user = Session::get('user');

        if (!Session::has('check_in')) {
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
    try {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['error' => 'User not logged in'], 401);
        }

        $month = $request->get('month'); // 'YYYY-MM'
        $singleUserId = $request->get('singleUserId'); // optional for admin

        // Determine which user's data to show
        $userId = $singleUserId ?? $user['id'];

        // Determine date range for the month
        $start = $month . '-01';
        $end = date("Y-m-t", strtotime($start));

        // Fetch attendance records for that user in that month
        $attendances = DB::table('police_attendance')
            ->where('police_user_id', $userId)
            ->whereBetween('attendance_date', [$start, $end])
            ->get();

        // Prepare events for the calendar
        $events = [];
        foreach ($attendances as $att) {
            // Assign different colors for each status
            $color = match ($att->status) {
                'Present' => 'green',
                'Leave'   => '#ffb703', // yellowish-orange
                'Absent'  => 'red',
                default   => '#6c757d', // gray for undefined
            };

            $events[] = [
                'title' => $att->status,
                'start' => $att->attendance_date,
                'color' => $color,
            ];
        }

        return response()->json($events);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to load attendance data.',
            'message' => $e->getMessage() // Optional for debugging
        ], 500);
    }
}


public function singleAttendance($singleUserId)
{
    $attendance = DB::table('police_attendance AS pa')
        ->join('police_users AS u', 'pa.police_user_id', '=', 'u.id')
        ->leftJoin('police_stations AS s', 'pa.police_station_id', '=', 's.id')
        ->where('pa.police_user_id', $singleUserId)
        ->select('pa.*', 'u.police_name', 's.name AS station_name')
        ->first();

    if (!$attendance) {
        return redirect()->back()->with('error', 'Attendance record not found.');
    }

    return view('attendance.create', compact('attendance', 'singleUserId'));
}


public function manualMark(Request $request)
{
    try {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not logged in.']);
        }

        $userId = $request->input('user_id') ?? $user['id'];
        $date = $request->input('date');
        $status = $request->input('status');

        // Prevent marking future dates
        if (strtotime($date) > strtotime(date('Y-m-d'))) {
            return response()->json(['status' => 'error', 'message' => 'You cannot mark future attendance.']);
        }

        // Check if already marked
        $exists = DB::table('police_attendance')
            ->where('police_user_id', $userId)
            ->whereDate('attendance_date', $date)
            ->exists();

        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Attendance already marked for this date.']);
        }

        // Insert attendance record
        DB::table('police_attendance')->insert([
            'police_user_id' => $userId,
            'attendance_date' => $date,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => "Marked as $status for $date"]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
    }
}

}
