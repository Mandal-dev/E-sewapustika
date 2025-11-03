<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PoliceAttendanceController extends Controller
{
    // Display all attendance records using Query Builder
public function index(Request $request)
{
    try {
        $user = Session::get('user');
        if (!$user) return redirect('/');

        $userId = $user['id'];
        $attendance = collect();

        // 🎯 If logged user is police personnel
        if ($user['designation_type'] === 'Police') {
            return view('attendance.create', ['userId' => $userId]);
        }

        // 📌 Common base query
        $query = DB::table('police_attendance AS pa')
            ->join('police_users AS u', 'pa.police_user_id', '=', 'u.id')
            ->leftJoin('police_stations AS s', 'pa.police_station_id', '=', 's.id')
            ->select(
                'pa.id AS attendance_id',
                'u.id AS user_id',
                'u.police_name',
                'u.designation_type',
                's.name AS station_name',
                'pa.police_user_id',
                'pa.police_station_id',
                'pa.attendance_date',
                'pa.status',
                'pa.checkin_time',
                'pa.checkout_time'
            )
            ->orderBy('pa.attendance_date', 'desc');

        // 🎯 STATION_HEAD
        if ($user['designation_type'] === 'Station_Head') {
            $myStationId = DB::table('police_users')
                ->where('id', $userId)
                ->value('police_station_id');

            $attendance = $query
                ->where('u.police_station_id', $myStationId)
                ->paginate(10);
        }

        // 🎯 LEAVE_DEPARTMENT
        elseif ($user['designation_type'] === 'Leave_Department') {
            $today = Carbon::today()->toDateString();
            $myStationId = DB::table('police_users')->where('id', $user['id'])->value('police_station_id');

            $attendance = DB::table('police_users AS pu')
                ->leftJoin('police_attendance AS t4', function ($join) use ($today) {
                    $join->on('pu.id', '=', 't4.police_user_id')
                        ->whereDate('t4.attendance_date', '=', $today);
                })
                ->join('police_stations AS ps', 'ps.id', '=', 'pu.police_station_id')
                ->where('pu.police_station_id', $myStationId)
                ->select(
                    'pu.id AS user_id',
                    'pu.police_name',
                    'ps.name AS station_name',
                    DB::raw("COALESCE(t4.status, 'Not Marked') AS status"),
                    't4.attendance_date'
                )
                ->orderBy('pu.police_name', 'asc')
                ->paginate(10);
        }

        // 🎯 DEPARTMENT LEVEL (District-wise)
        elseif (in_array($user['designation_type'], [
            'Head_Person', 'Punishment_Department', 'Rewards_Department',
            'Account_Department', 'Sewapustika_Department'
        ])) {

            $today = Carbon::today()->toDateString();
            $myDistrictId = DB::table('police_users')->where('id', $user['id'])->value('district_id');

            $attendance = DB::table('police_users AS pu')
                ->join('police_stations AS ps', 'ps.id', '=', 'pu.police_station_id')
                ->leftJoin('police_attendance AS t4', function ($join) use ($today) {
                    $join->on('pu.id', '=', 't4.police_user_id')
                        ->whereDate('t4.attendance_date', '=', $today);
                })
                ->join('districts AS d', 'd.id', '=', 'pu.district_id')
                ->where('pu.district_id', $myDistrictId)
                ->select(
                    'ps.name AS station_name',
                    'pu.id AS user_id',
                    'pu.police_name',
                    DB::raw("d.district_name"),
                    DB::raw("COALESCE(t4.status, 'Not Marked') AS status"),
                    't4.attendance_date'
                )
                ->orderBy('pu.police_name', 'asc')
                ->paginate(10);
        }

        // 🎯 ADMIN — all attendance data
        elseif ($user['designation_type'] === 'Admin') {
            $attendance = $query->paginate(10);
        }

        // ⚡ Return only table for AJAX pagination
        if ($request->ajax()) {
            return view('attendance.table', compact('attendance'))->render();
        }

        // 📄 Default (full page)
        return view('attendance.index', compact('attendance'));

    } catch (\Exception $e) {
        Log::error('Attendance Index Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
    }
}




    // Show form to create attendance
    public function create()
    {
        return view('attendance.create');
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
        try {
            // ✅ Get user from session
            $user = Session::get('user');
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not logged in'
                ]);
            }

            // ✅ Use Indian timezone
            $now = Carbon::now('Asia/Kolkata');
            $today = $now->toDateString();
            $currentTime = $now->format('H:i:s');

            // ✅ Check if already checked in today
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

            // ✅ Fetch district & police station info
            $policeData = DB::table('police_users')
                ->where('id', $user['id'])
                ->select('district_id', 'police_station_id')
                ->first();

            if (!$policeData || !$policeData->district_id || !$policeData->police_station_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User data incomplete'
                ]);
            }

            // ✅ Prepare and insert attendance record
            $insertData = [
                'police_user_id'    => $user['id'],
                'police_station_id' => $policeData->police_station_id,
                'district_id'       => $policeData->district_id,
                'attendance_date'   => $today,
                'status'            => 'Present',
                'checkin_time'      => $currentTime,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            $insertedId = DB::table('police_attendance')->insertGetId($insertData);

            // ✅ Store session check-in info
            Session::put('check_in', $currentTime);

            // ✅ Return success
            return response()->json([
                'status' => 'success',
                'message' => "Checked in successfully at {$currentTime}",
                'data' => [
                    'attendance_id' => $insertedId,
                    'user_id' => $user['id'],
                    'date' => $today,
                    'time' => $currentTime,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
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
        // Get user and station details
        $user = DB::table('police_users AS u')
            ->leftJoin('police_stations AS s', 'u.police_station_id', '=', 's.id')
            ->select('u.id', 'u.police_name', 's.name AS station_name')
            ->where('u.id', $singleUserId)
            ->first();

        // Get latest attendance record for that user (if exists)
        $attendance = DB::table('police_attendance AS pa')
            ->where('pa.police_user_id', $singleUserId)
            ->orderByDesc('pa.attendance_date')
            ->first();

        // If no attendance found, create a blank object with user details
        if (!$attendance) {
            $attendance = (object) [
                'id' => null,
                'police_user_id' => $user->id ?? $singleUserId,
                'status' => '',
                'attendance_date' => '',
                'station_name' => $user->station_name ?? '',
                'police_name' => $user->police_name ?? '',
            ];
        } else {
            // Add user and station details for view
            $attendance->station_name = $user->station_name ?? '';
            $attendance->police_name = $user->police_name ?? '';
        }

        // Return view with attendance data (blank or existing)
        return view('attendance.create', compact('attendance', 'singleUserId'));
    }


public function manualMark(Request $request)
{
    try {
        $sessionUser = Session::get('user');

        // 🔹 Check login
        if (!$sessionUser) {
            Log::warning('Manual Attendance: User not logged in');
            return response()->json(['status' => 'error', 'message' => 'User not logged in']);
        }

        // 🔹 Authorization check
        if (!in_array($sessionUser['designation_type'], ['Head_Person', 'Leave_Department'])) {
            Log::warning("Manual Attendance Denied: user_id={$sessionUser['id']} ({$sessionUser['designation_type']}) not authorized");
            return response()->json(['status' => 'error', 'message' => 'Unauthorized']);
        }

        // 🔹 Input validation
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:police_users,id',
            'date' => 'required|date',
            'status' => 'required|in:Present,Absent,Leave,Half-Day',
        ]);

        $userId = $validated['user_id'] ?? $sessionUser['id'];
        $date = $validated['date'];
        $status = $validated['status'];

        // 🔹 Prevent marking future attendance
        if (Carbon::parse($date)->gt(Carbon::today())) {
            return response()->json(['status' => 'error', 'message' => 'Cannot mark future attendance']);
        }

        // 🔹 Fetch user’s police details
        $policeData = DB::table('police_users')
            ->where('id', $userId)
            ->select('district_id', 'police_station_id')
            ->first();

        if (!$policeData) {
            Log::warning("Manual Attendance: Missing police data for user_id={$userId}");
            return response()->json(['status' => 'error', 'message' => 'User data incomplete']);
        }

        $currentTime = Carbon::now('Asia/Kolkata');

        DB::beginTransaction();

        // 1️⃣ Attendance Insert/Update
        $existing = DB::table('police_attendance')
            ->where('police_user_id', $userId)
            ->whereDate('attendance_date', $date)
            ->first();

        if ($existing) {
            DB::table('police_attendance')
                ->where('id', $existing->id)
                ->update([
                    'status' => $status,
                    'updated_at' => $currentTime,
                ]);
            Log::info("Manual Attendance Updated: user_id={$userId}, date={$date}, status={$status}");
        } else {
            DB::table('police_attendance')->insert([
                'police_user_id'    => $userId,
                'police_station_id' => $policeData->police_station_id,
                'district_id'       => $policeData->district_id,
                'attendance_date'   => $date,
                'status'            => $status,
                'created_at'        => $currentTime,
                'updated_at'        => $currentTime,
            ]);
            Log::info("Manual Attendance Inserted: user_id={$userId}, date={$date}, status={$status}");
        }

        // 2️⃣ Handle Leave Deduction (only for "Absent")
        if ($status === 'Absent') {
            $this->deductLeave($userId, $currentTime);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => "Attendance marked successfully as '$status' for $date",
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error("Manual Attendance Error: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
    }
}

/**
 * Deduct leave if user is absent.
 */
private function deductLeave(int $userId, Carbon $currentTime): void
{
    $leaveRecord = DB::table('police_leaves')
        ->where('police_user_id', $userId)
        ->lockForUpdate()
        ->first();

    $currentMonth = now()->month;
    $leavePeriod = ($currentMonth <= 6) ? 'H1' : 'H2';
    $maxLeaves = 15;

    if (!$leaveRecord) {
        Log::warning("No leave record found for user_id={$userId} during {$leavePeriod}");
        return;
    }

    $used = $leaveRecord->used_leaves ?? 0;
    $remaining = $maxLeaves - $used;

    if ($remaining > 0) {
        DB::table('police_leaves')
            ->where('police_user_id', $userId)
            ->update([
                'used_leaves' => DB::raw('used_leaves + 1'),
                'updated_at' => $currentTime,
            ]);
        Log::info("Leave deducted for user_id={$userId} ({$leavePeriod})");
    } else {
        DB::table('police_leaves')
            ->where('police_user_id', $userId)
            ->update([
                'extra_leaves' => DB::raw('COALESCE(extra_leaves, 0) + 1'),
                'used_leaves' => DB::raw('used_leaves + 1'),
                'updated_at' => $currentTime,
            ]);
        Log::warning("Extra leave applied for user_id={$userId} ({$leavePeriod})");
    }
}




    public function checkStatus(Request $request)
    {
        $userId = $request->input('user_id');
        $date = $request->input('date');

        $attendance = DB::table('police_attendance')
            ->where('police_user_id', $userId)
            ->whereDate('attendance_date', $date)
            ->select('status', 'checkin_time', 'checkout_time')
            ->first();

        if ($attendance) {
            return response()->json([
                'status' => 'marked',
                'attendance_status' => $attendance->status,
                'checkin_time' => $attendance->checkin_time,
                'checkout_time' => $attendance->checkout_time,
            ]);
        } else {
            return response()->json(['status' => 'not_marked']);
        }
    }

public function checkLeavetrack(Request $request, $userid)
{
    try {
        $year = $request->input('year', date('Y')); // Default to current year

        $leaveData = DB::table('police_leaves')
            ->select(
                DB::raw('SUM(total_leaves) as total_leaves'),
                DB::raw('SUM(used_leaves) as used_leaves'),
                DB::raw('SUM(extra_leaves) as extra_leaves'),
                DB::raw('SUM(total_leaves - used_leaves) as remaining_leaves')
            )
            ->where('police_user_id', $userid)
            ->where('leave_type', 'CL') // You can also extend for other types
            ->whereYear('created_at', $year) // Filter for selected year
            ->first();

        if ($leaveData && ($leaveData->total_leaves > 0 || $leaveData->used_leaves > 0)) {
            // ✅ Found combined data for the selected year
            return response()->json([
                'status' => 'success',
                'user_id' => $userid,
                'year' => $year,
                'total_leaves' => $leaveData->total_leaves,
                'used_leaves' => $leaveData->used_leaves,
                'extra_leaves' => $leaveData->extra_leaves,
                'remaining_leaves' => $leaveData->remaining_leaves,
            ]);
        } else {
            // ✅ No record found for this year → return default
            return response()->json([
                'status' => 'success',
                'user_id' => $userid,
                'year' => $year,
                'total_leaves' => 0,
                'used_leaves' => 0,
                'extra_leaves' => 0,
                'remaining_leaves' => 0,
            ]);
        }
    } catch (\Exception $e) {
        Log::error("Leave Track Error for user_id={$userid}, year={$year}: " . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong while checking leave track'
        ]);
    }
}


    public function checkOut(Request $request)
    {
        try {
            // Get user from session
            $user = Session::get('user');
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not logged in'
                ]);
            }

            // Use Indian timezone
            $now = Carbon::now('Asia/Kolkata');
            $today = $now->toDateString();
            $currentTime = $now->format('H:i:s');

            // ✅ Find today’s attendance record
            $attendance = DB::table('police_attendance')
                ->where('police_user_id', $user['id'])
                ->where('attendance_date', $today)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have not checked in today'
                ]);
            }

            // ✅ Prevent multiple check-outs
            if (!empty($attendance->checkout_time)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already checked out today'
                ]);
            }

            // ✅ Update record with checkout time
            DB::table('police_attendance')
                ->where('id', $attendance->id)
                ->update([
                    'checkout_time' => $currentTime,
                    'updated_at' => $now,
                ]);

            // ✅ Clear session check-in info
            Session::forget('check_in');

            // ✅ Success response
            return response()->json([
                'status' => 'success',
                'message' => "Checked out successfully at {$currentTime}",
                'data' => [
                    'attendance_id' => $attendance->id,
                    'user_id' => $user['id'],
                    'date' => $today,
                    'checkout_time' => $currentTime,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }

    public function checkinCheckOutStatus()
    {
        try {
            // ✅ Get user from session
            $user = Session::get('user');
            if (!$user || !isset($user['id'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not logged in'
                ]);
            }

            $userId = $user['id'];

            // ✅ Use Indian timezone and today's date
            $today = \Carbon\Carbon::now('Asia/Kolkata')->toDateString();

            // ✅ Fetch today's attendance
            $attendance = DB::table('police_attendance')
                ->where('police_user_id', $userId)
                ->whereDate('attendance_date', $today)
                ->select('checkin_time', 'checkout_time')
                ->first();

            // ✅ Return proper response
            if ($attendance) {
                return response()->json([
                    'status' => 'found',
                    'date' => $today,
                    'checkin_time' => $attendance->checkin_time,
                    'checkout_time' => $attendance->checkout_time,
                ]);
            }

            return response()->json([
                'status' => 'not_found',
                'date' => $today,
                'message' => 'No attendance record found for today'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }

 public function search(Request $request)
{
    try {
        $user = Session::get('user');
        if (!$user) return redirect('/');

        $userId = $user['id'];
        $search = $request->input('search');
        $today = Carbon::today()->toDateString();

        // Base query: all users + today's attendance if exists
        $query = DB::table('police_users AS u')
            ->leftJoin('police_attendance AS pa', function ($join) use ($today) {
                $join->on('u.id', '=', 'pa.police_user_id')
                    ->whereDate('pa.attendance_date', '=', $today);
            })
            ->leftJoin('police_stations AS s', 'u.police_station_id', '=', 's.id')
            ->select(
                'u.id AS user_id',
                'u.police_name',
                'u.designation_type',
                's.name AS station_name',
                'pa.attendance_date',
                DB::raw("COALESCE(pa.status, 'Not Marked') AS status"),
                'pa.checkin_time',
                'pa.checkout_time'
            )
            ->orderBy('u.police_name', 'asc');

        // Apply search filters
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('u.police_name', 'LIKE', "%{$search}%")
                  ->orWhere('s.name', 'LIKE', "%{$search}%")
                  ->orWhere('s.name', 'LIKE', "%{$search}%")
                  ->orWhere('pa.status', 'LIKE', "%{$search}%")
                  ->orWhere('u.buckle_number', 'LIKE', "%{$search}%")
                  ->orWhere('u.mobile', 'LIKE', "%{$search}%");
            });
        }

        // Role-specific filters
        if ($user['designation_type'] === 'Station_Head') {
            $myStationId = DB::table('police_users')->where('id', $userId)->value('police_station_id');
            $query->where('u.police_station_id', $myStationId);
        }

        elseif ($user['designation_type'] === 'Leave_Department') {
            $myStationId = DB::table('police_users')->where('id', $userId)->value('police_station_id');
            $query->where('u.police_station_id', $myStationId);
        }

        elseif (in_array($user['designation_type'], [
            'Head_Person',
            'Punishment_Department',
            'Rewards_Department',
            'Account_Department',
            'Sewapustika_Department'
        ])) {
            $myDistrictId = DB::table('police_users')->where('id', $userId)->value('district_id');
            $query->where('u.district_id', $myDistrictId);
        }

        // Paginate and retain search parameter
        $attendance = $query->paginate(10)->appends(['search' => $search]);

        // For AJAX search
        if ($request->ajax()) {
            return view('attendance.table', compact('attendance'))->render();
        }

        // For normal full page
        return view('attendance.table', compact('attendance', 'search'));

    } catch (\Exception $e) {
        Log::error('Attendance Search Error: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong.'], 500);
    }
}


}
