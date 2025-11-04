<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class SalaryIncrementController extends Controller

{
    public function index()
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/')->with('error', 'Session expired. Please login again.');
            }

            $perPage = 10;

            $query = DB::table('police_users AS t4')
                ->leftJoin('districts AS t2', function ($join) {
                    $join->on('t4.district_id', '=', 't2.id')
                        ->where(function ($q) {
                            $q->where('t2.is_delete', 'No')
                                ->orWhereNull('t2.is_delete');
                        })
                        ->where(function ($q) {
                            $q->where('t2.status', 'Active')
                                ->orWhereNull('t2.status');
                        });
                })

                ->leftJoin('police_stations AS t7', 't4.police_station_id', '=', 't7.id')

                // ✅ Join salary increments (latest for each police_id)
                ->leftJoin('salary_increments AS t5', function ($join) {
                    $join->on('t4.id', '=', 't5.police_id')
                        ->whereRaw('t5.id IN (SELECT MAX(id) FROM salary_increments GROUP BY police_id)');
                })

                // ✅ Join salary reviews
                ->leftJoin('salary_reviews AS t6', 't5.id', '=', 't6.salary_id')

                ->select(


                    't2.id AS district_id',
                    't2.district_name',
                    't7.name AS police_station_name',

                    't4.id AS police_user_id',
                    't4.police_name',
                    't4.buckle_number',
                    't4.designation_type AS role',

                    't5.id AS salary_increment_id',
                    't5.increment_type',
                    't5.increment_date',
                    't5.increment_documents',
                    't5.new_salary',
                    't5.level',
                    't5.grade_pay',
                    't5.increased_amount',
                    't5.present_days',

                    't6.remark',

                     DB::raw('
                    CASE
                        WHEN t5.id IS NOT NULL AND t6.id IS NULL THEN "pending"
                        WHEN t5.id IS NULL AND t6.id IS NULL THEN "not_uploaded"
                        WHEN t6.review_status IS NOT NULL THEN t6.review_status
                        ELSE "not_uploaded"
                    END AS salary_status
                ')

                );

            // ✅ Role-based filter
            switch ($user['designation_type']) {
                case 'Police':
                    $query->where('t4.id', $user['id']);
                    break;
                case 'Leave_Department':
                    $query->where('t4.id', $user['id']);
                    break;
                case 'Station_Head':
                    $myStationId = DB::table('police_users')
                        ->where('id', $user['id'])
                        ->value('police_station_id');
                    $query->where('t4.police_station_id', $myStationId);
                    break;

                case 'Head_Person':
                    $query->where('t4.district_id', $user['district_id']);
                    break;

                case 'Account_Department':
                    $query->where('t4.district_id', $user['district_id']);
                    break;

                case 'Admin':
                    // no filter
                    break;

                default:
                    return redirect('/')->with('error', 'Unauthorized access.');
            }

            // ✅ Proper pagination
            $polices = $query->orderBy('t4.id', 'desc')->paginate($perPage);

            return view('Salary_Increment.index', compact('polices'));
        } catch (\Exception $e) {
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            return view('Salary_Increment.index', [
                'polices' => $emptyPaginator,
                'error'   => $e->getMessage()
            ]);
        }
    }


    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $designationFilter = $request->input('designation'); // optional

            $user = Session::get('user');
            if (!$user) {
                return response()->json(['message' => 'Unauthorized. Please login.'], 401);
            }

            // 🔹 Get latest salary increment per police_id
            $latestIncrement = DB::table('salary_increments')
                ->select(
                    'id',
                    'police_id',
                    'increment_type',
                    'increment_documents',
                    'increment_date',
                    'new_salary',
                    'level',
                    'grade_pay',
                    'increased_amount'
                )
                ->whereRaw('id IN (SELECT MAX(id) FROM salary_increments GROUP BY police_id)');

            // 🔹 Base query for police users
            $query = DB::table('police_users AS t4')
                ->join('districts AS t2', 't4.district_id', '=', 't2.id')
                ->leftJoin('police_stations AS t7', 't4.police_station_id', '=', 't7.id')
                ->leftJoinSub($latestIncrement, 't5', function ($join) {
                    $join->on('t4.id', '=', 't5.police_id');
                })
                ->leftJoin('salary_reviews AS t6', 't5.id', '=', 't6.salary_id')
                ->select(
                    't2.district_name',
                    't7.name AS police_station_name',
                    't4.id AS police_user_id',
                    't4.police_name',
                    't4.buckle_number',
                    't4.designation_type',
                    't5.increment_date',
                    't5.increment_type',
                    't5.increment_documents',
                    't5.id AS salary_increment_id',
                    't5.new_salary',
                    't5.level',
                    't5.grade_pay',
                    't5.increased_amount',
                    't6.remark',
                    DB::raw('
                    CASE
                        WHEN t5.id IS NOT NULL AND t6.id IS NULL THEN "pending"
                        WHEN t5.id IS NULL AND t6.id IS NULL THEN "not_uploaded"
                        WHEN t6.review_status IS NOT NULL THEN t6.review_status
                        ELSE "not_uploaded"
                    END AS salary_status
                ')
                )
                ->where('t2.is_delete', 'No')
                ->where('t2.status', 'Active');

            // 🔹 Role-based filter
            switch ($user['designation_type']) {
                case 'Police':
                    return response()->json(['message' => 'Access denied.'], 403);
                case 'Station_Head':
                    $myStationId = DB::table('police_users')
                        ->where('id', $user['id'])
                        ->value('police_station_id');
                    $query->where('t4.police_station_id', $myStationId);
                    break;
                case 'Head_Person':
                case 'Account_Department':
                    $query->where('t4.district_id', $user['district_id']);
                    break;
                case 'Admin':
                    // No restriction
                    break;
                default:
                    return response()->json(['message' => 'Invalid role.'], 403);
            }

            // 🔹 Search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $statusMap = [
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                        'pending'  => 'pending',
                        'uploaded' => 'pending', // ✅ uploaded = pending
                        'all'      => null
                    ];

                    if (isset($statusMap[strtolower($search)]) && strtolower($search) != 'all') {
                        $q->whereRaw('
                        CASE
                            WHEN t5.id IS NOT NULL AND t6.id IS NULL THEN "pending"
                            WHEN t5.id IS NULL AND t6.id IS NULL THEN "not_uploaded"
                            WHEN t6.review_status IS NOT NULL THEN t6.review_status
                            ELSE "not_uploaded"
                        END = ?
                    ', [$statusMap[strtolower($search)]]);
                    } else {
                        $q->where('t4.police_name', 'like', "%{$search}%")
                            ->orWhere('t4.buckle_number', 'like', "%{$search}%")
                            ->orWhere('t7.name', 'like', "%{$search}%")
                            ->orWhere('t6.review_status', 'like', "%{$search}%")
                            ->orWhere('t2.district_name', 'like', "%{$search}%");
                    }
                });
            }

            // 🔹 Designation filter
            if ($designationFilter) {
                $query->where('t4.designation_type', $designationFilter);
            }

            // 🔹 Execute query
            $polices = $query->orderBy('t4.id', 'desc')->get();

            // 🔹 Return rendered view
            return view('Salary_Increment.table_rows', compact('polices'))->render();
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'An error occurred while searching salary increments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function policeSalaryIncrementAdd($id)
    {
        $user = Session::get('user');

        // Check if user is logged in
        if (!$user) {
            return response()->json(['error' => 'कृपया लॉगिन करा.'], 403);
        }

        // Only Head_Person can access


        // Get police user only if in the same district
        $police = DB::table('police_users AS t4')
            ->join('districts AS t2', 't4.district_id', '=', 't2.id')
            ->join('states AS t1', 't2.state_id', '=', 't1.id')
            ->join('cities AS t3', 't4.city_id', '=', 't3.id')
            ->select(
                't4.id AS police_user_id',
                't4.police_name',
                't4.buckle_number',
                't1.id AS state_id',
                't1.state_name',
                't2.id AS district_id',
                't2.district_name',
                't3.id AS city_id',
                't3.city_name',
                't3.status AS city_status'
            )
            ->where('t4.is_delete', 'No')
            ->where('t4.district_id', $user['district_id'])
            ->where('t4.id', $id)
            ->first();
        $grade_pay_options = DB::table('stages')->orderBy('id')->get();
        $pay_lavels_options = DB::table('levels')->orderBy('id')->get();
        if (!$police) {
            return response()->json(['error' => 'सदर पोलीस वापरकर्ता अस्तित्वात नाही किंवा आपल्याला परवानगी नाही.'], 404);
        }

        return view('Salary_Increment.add', compact('police', 'grade_pay_options', 'pay_lavels_options'));
    }


    public function storeSalaryIncrement(Request $request)
    {
        $user = Session::get('user');

        // Only Head_Person can access
        if (!$user || $user['designation_type'] !== 'Head_Person') {
            return redirect()->back()->with('error', 'आपल्याला वेतनवाढ जोडण्याची परवानगी नाही.');
        }

        // Ensure police_id belongs to the same district
        $policeDistrict = DB::table('police_users')
            ->where('id', $request->police_id)
            ->value('district_id');

        if ($policeDistrict != $user['district_id']) {
            return redirect()->back()->with('error', 'आपल्याला इतर जिल्ह्यातील अधिकारीसाठी वेतनवाढ जोडण्याची परवानगी नाही.');
        }

        // Validation
        $request->validate([
            'police_id'           => 'required|integer',
            'district_id'         => 'required|integer',
            'station_id'          => 'required|integer',
            'increment_date'      => 'required|date',
            'increment_type'      => 'required|string',
            'new_salary'          => 'required|numeric',
            'level_no'            => 'nullable|string|max:10',
            'grade_pay'           => 'nullable|string',
            'increased_amount'    => 'required|numeric',
            'increment_documents' => 'nullable|file|mimes:pdf|max:5120',
            'present_days'        => 'required|integer|min:0',
        ]);

        // Attendance check
        if ($request->present_days < 180) {
            return redirect()->back()->with('error', 'उपस्थिती 180 दिवसांपेक्षा कमी असल्यामुळे वेतनवाढ मंजूर होऊ शकत नाही.');
        }

        // 🛑 NEW: Punishment-year check ----------------------------
        $incrementDate = $request->increment_date;
        $selectedYear = date('Y', strtotime($incrementDate));

        $hasPunishment = DB::table('police_punishments')
            ->where('police_id', $request->police_id)
            ->whereYear('punishment_given_date', $selectedYear)
            ->exists();

        if ($hasPunishment) {
            return redirect()->back()->with(
                'error',
                "सदर पोलीस अधिकाऱ्यास $selectedYear मध्ये शिक्षा देण्यात आली आहे, त्यामुळे वेतनवाढ मंजूर होऊ शकत नाही."
            );
        }
        // ----------------------------------------------------------

        try {
            $uniqueFileName = null;

            // Handle file upload
            if ($request->hasFile('increment_documents')) {
                $file = $request->file('increment_documents');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $uniqueFileName = now()->format('Ymd_His') . '_' . Str::slug($originalName) . '.' . $extension;

                $destinationPath = base_path('uploads/salaryincrements');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $uniqueFileName);
            }

            $level_value = 'L-' . $request->level_no;
            $grade_value = 'S-' . $request->grade_pay;

            // Insert salary increment
            DB::table('salary_increments')->insert([
                'police_id'           => $request->police_id,
                'district_id'         => $request->district_id,
                'station_id'          => $request->station_id,
                'increment_documents' => $uniqueFileName,
                'increment_date'      => $request->increment_date,
                'increment_type'      => $request->increment_type,
                'new_salary'          => $request->new_salary,
                'level'               => $level_value,
                'grade_pay'           => $grade_value,
                'increased_amount'    => $request->increased_amount,
                'present_days'        => $request->present_days,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            return redirect()->back()->with('success', 'वेतनवाढ माहिती यशस्वीरित्या जतन झाली.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'वेतनवाढ जतन करताना त्रुटी आली: ' . $e->getMessage());
        }
    }

    public function view($filename)
    {
        $path = base_path('uploads/salaryincrements/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'File not found.');
        }

        // Optional: check MIME type dynamically
        $mime = File::mimeType($path);

        return response()->file($path, [
            'Content-Type' => $mime ?? 'application/pdf'
        ]);
    }

    //get salary by lavel and grade pay
    public function getSalary(Request $request)
    {
        $levelId = $request->get('level_no');   // from dropdown
        $stageId = $request->get('grade_pay');  // from dropdown



        $salary = DB::table('pay_matrix')
            ->where('level_id', $levelId)
            ->where('stage_id', $stageId)
            ->value('amount');



        return response()->json(['salary' => $salary]);
    }

    /**
     * Show salary approval details
     */
    public function show($salaryId)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/')->with('error', 'Session expired. Please login again.');
            }

            // ✅ Get salary details + reviews + police user + master data
            $salary = DB::table('salary_increments AS si')
                ->leftJoin('salary_reviews AS sr', 'si.id', '=', 'sr.salary_id')
                ->leftJoin('police_users AS pu', 'si.police_id', '=', 'pu.id')
                ->leftJoin('districts AS d', 'pu.district_id', '=', 'd.id')
                ->leftJoin('states AS s', 'd.state_id', '=', 's.id')
                ->leftJoin('cities AS c', 'pu.city_id', '=', 'c.id')
                ->select(
                    'si.id AS salary_id',
                    'si.police_id',
                    'si.increment_type',
                    'si.increment_documents',
                    'si.increment_date',
                    'si.new_salary',
                    'si.level',
                    'si.grade_pay',
                    'si.increased_amount',
                    'si.present_days',

                    // Review table
                    'sr.id AS review_id',
                    DB::raw('COALESCE(sr.review_status, "Pending") AS review_status'),

                    'sr.remark',


                    // Police user
                    'pu.police_name',
                    'pu.buckle_number',
                    'pu.designation_type AS role',
                    'pu.post',

                    // Master data
                    's.state_name',
                    'd.district_name',
                    'c.city_name'
                )
                ->where('si.id', $salaryId)
                ->first();

            if (!$salary) {
                return redirect()->back()->with('error', 'Salary record not found.');
            }

            return view('Salary_Increment.salary_aprove', compact('salary'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function approveSalaryIncrementStore(Request $request)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthenticated. Please login.');
        }

        // Only Head_Person can approve/reject
        if ($user['designation_type'] !== 'Head_Person') {
            return redirect()->back()->with('error', 'Access denied. Only Head_Person can approve/reject.');
        }

        // Validation
        $request->validate([
            'salary_id' => 'required|integer|exists:salary_increments,id',
            'status'    => 'required|in:Approved,Rejected',
            'remark'    => 'nullable|string|max:500',
        ]);

        // Prevent duplicate review
        $existingReview = DB::table('salary_reviews')
            ->where('salary_id', $request->salary_id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'This salary increment has already been reviewed.');
        }

        $data = [
            'salary_id'     => $request->salary_id,
            'reviewed_by'   => $user['id'],
            'review_status' => strtolower($request->status), // store as lowercase
            'remark'        => $request->remark ?? null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];

        try {
            DB::table('salary_reviews')->insert($data);
            return redirect()->back()->with('success', 'Salary increment review stored successfully.');
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Failed to store salary increment review.');
        }
    }


    public function salary_increment_cards()
    {
        $user = Session::get('user');

        $counts = DB::table('police_users AS t4')
            ->leftjoin('salary_increments AS s', 't4.id', 's.police_id')
            ->leftJoin('salary_reviews AS r', 's.id', '=', 'r.salary_id')
            ->where('t4.district_id', $user['district_id'])
            ->select(
                DB::raw('COUNT(DISTINCT t4.id) AS total_police'),
                DB::raw('COUNT(DISTINCT s.id) AS total_uploaded'),
                DB::raw('SUM(CASE WHEN r.review_status = "approved" THEN 1 ELSE 0 END) AS approved'),
                DB::raw('SUM(CASE WHEN r.review_status = "rejected" THEN 1 ELSE 0 END) AS rejected'),
                DB::raw('SUM(CASE WHEN s.id IS NOT NULL AND r.id IS NULL THEN 1 ELSE 0 END) AS pending')
            )
            ->first();

        $stats = [
            'title' => 'Salary Increment',
            'total_police' => $counts->total_police,
            'total_uploaded' => $counts->total_uploaded,
            'approved' => $counts->approved,
            'rejected' => $counts->rejected,
            'pending' => $counts->pending
        ];

        return view('cards.salary_increment_cards', ['stats' => $stats]);
    }
}
