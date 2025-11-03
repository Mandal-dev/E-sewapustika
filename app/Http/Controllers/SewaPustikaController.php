<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;


class SewaPustikaController extends Controller
{
    public function index()
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/');
            }

            $perPage = 10;

            // latest pustika subquery
            $latestPustika = DB::table('sewa_pustikas')
                ->select('id', 'police_id', 'sewa_pustika_status', 'sewapusticapath')
                ->whereRaw('id IN (SELECT MAX(id) FROM sewa_pustikas GROUP BY police_id)');

            // base query
            $query = DB::table('police_users AS t4')
                ->leftJoin('districts AS t2', 't4.district_id', '=', 't2.id')
                ->leftJoin('states AS t1', 't2.state_id', '=', 't1.id')
                ->leftJoin('cities AS t3', 't4.city_id', '=', 't3.id')
                ->leftJoin('police_stations AS t6', 't4.police_station_id', '=', 't6.id')

                ->leftJoinSub($latestPustika, 't5', function ($join) {
                    $join->on('t4.id', '=', 't5.police_id');
                })
                ->leftJoin('sewapushtika_review AS t7', 't5.id', '=', 't7.sewapustika_id') // ✅ FIXED
                ->select(
                    't1.state_name',
                    't1.id AS state_id',
                    't2.id AS district_id',
                    't2.district_name',
                    't3.id AS city_id',
                    't3.city_name',
                    't4.id AS police_user_id',
                    't4.police_name',
                    't4.buckle_number',
                    't5.sewa_pustika_status',
                    't5.id AS sewapustika_id',
                    't6.name AS police_station_name',
                    't5.sewapusticapath',
                    't4.post',
                    't4.mobile',
                    't7.remark',
                    DB::raw('COALESCE(t7.review_status, "Pending") AS review_status')
                )
                ->where('t4.is_delete', 'No');

            // designation-based filters
            switch ($user['designation_type']) {
                case 'Police':
                    $query->where('t4.id', $user['id']);
                    break;
                case 'Leave_Department':
                    $query->where('t4.id', $user['id']);
                    break;
                case 'Station_Head':
                    $myStationId = DB::table('police_users')->where('id', $user['id'])->value('police_station_id');
                    $query->where('t4.police_station_id', $myStationId);
                    break;

                case 'Head_Person':
                    $query->where('t4.district_id', $user['district_id']);
                    break;
                case 'Sewapustika_Department':
                    $query->where('t4.district_id', $user['district_id']);
                    break;

                case 'Admin':
                    // no extra filter
                    break;

                default:
                    return redirect()->back()->with('error', 'Unauthorized access.');
            }

            // paginate
            $polices = $query->orderBy('t4.id', 'desc')->paginate($perPage);

            return view('sewa_pustika.index', compact('polices'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function show($sewapustika_Id)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/')->with('error', 'Session expired. Please login again.');
            }

            // Example of latest Sewapustika subquery (adjust as per your logic)
            $latestPustika = DB::table('sewa_pustikas')
                ->select('id', 'police_id', 'sewa_pustika_status', 'sewapusticapath')
                ->whereIn('id', function ($q) {
                    $q->select(DB::raw('MAX(id)'))
                        ->from('sewa_pustikas')
                        ->groupBy('police_id');
                });

            $polices = DB::table('police_users AS t4')
                ->leftJoin('districts AS t2', 't4.district_id', '=', 't2.id')
                ->leftJoin('states AS t1', 't2.state_id', '=', 't1.id')
                ->leftJoin('cities AS t3', 't4.city_id', '=', 't3.id')
                ->leftJoin('police_stations AS t6', 't4.police_station_id', '=', 't6.id')
                ->leftJoin('sewa_pustikas AS t5',  't4.id', '=', 't5.police_id')

                ->leftJoin('sewapushtika_review AS t7', 't5.id', '=', 't7.sewapustika_id')
                ->select(
                    't1.state_name',
                    't1.id AS state_id',
                    't2.id AS district_id',
                    't2.district_name',
                    't3.id AS city_id',
                    't3.city_name',
                    't4.id AS police_user_id',
                    't4.police_name',
                    't4.buckle_number',
                    't5.sewa_pustika_status',
                    't5.id AS sewapustika_id',
                    't6.name AS police_station_name',
                    't5.sewapusticapath',
                    't4.post',
                    't4.mobile',

                    DB::raw('COALESCE(t7.review_status, "Pending") AS review_status')
                )
                ->where('t5.id', $sewapustika_Id) // ✅ filter by salaryId (or police_user id?)
                ->first();

            if (!$polices) {
                return redirect()->back()->with('error', 'Record not found.');
            }

            return view('sewa_pustika.sewa_pustika_aprove', compact('polices'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function showuploadpage($id)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/')->with('error', 'Please login first.');
        }

        // base query
        $query = DB::table('police_users AS t4')
            ->leftJoin('districts AS t2', 't4.district_id', '=', 't2.id')
            ->leftJoin('states AS t1', 't2.state_id', '=', 't1.id')
            ->leftJoin('cities AS t3', 't4.city_id', '=', 't3.id')
            ->leftJoin('police_stations AS t6', 't4.police_station_id', '=', 't6.id')
            ->select(
                't1.state_name',
                't1.id AS state_id',
                't2.id AS district_id',
                't2.district_name',
                't6.id AS station_id',
                't6.name',
                't4.id AS police_user_id',
                't4.police_name',
                't4.buckle_number',
                't6.name AS police_station_name',
                't3.status AS city_status'
            )
            ->where('t4.is_delete', 'No');

        // designation-based access
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
            case 'Sewapustika_Department':
                $query->where('t4.district_id', $user['district_id']);
                break;

            case 'Admin':
                // Admin can view any police record
                break;

            default:
                return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // fetch single record
        $police = $query->where('t4.id', $id)
            ->orderBy('t4.id', 'desc')
            ->first();

        if (!$police) {
            return redirect()->back()->with('error', 'Police record not found or access denied.');
        }

        return view('sewa_pustika.edit', compact('police'));
    }

    public function storeAprove(Request $request)
    {
        // Validate the request
        $request->validate([
            'sewapustika_id' => 'required|integer|exists:sewa_pustikas,id',
            'status' => 'required|in:Approved,Rejected',
            'remark' => 'nullable|string|max:255',
        ]);

        try {
            // Get user from session
            $user = Session::get('user');

            // If no session user and no auth user
            if (!$user && !auth()->check()) {
                return redirect()->back()->with('error', 'Session expired. Please login again.');
            }

            // Prepare data to insert
            $data = [
                'sewapustika_id' => $request->sewapustika_id,
                'reviewed_by' => $user['id'] ?? auth()->id(),
                'review_status' => strtolower($request->status),
                'remark' => $request->remark,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert into sewapushtika_review table
            DB::table('sewapushtika_review')->insert($data);

            return redirect()->back()->with('success', 'Sewapustika review saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function store(Request $request)
    {



        $user = Session::get('user');

        // ✅ Check if user is logged in
        if (!$user) {

            return redirect()->back()->with('error', 'कृपया लॉगिन करा.');
        }

        // Validation with logging
        try {
            $validated = $request->validate([
                'police_id'          => 'required|integer',
                'state_id'           => 'required|integer',
                'district_id'        => 'required|integer',

                'sewa_pustika_file'  => 'required|file|mimes:pdf|max:102400', // 100 MB
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()->withErrors($e->errors());
        }

        // Get police record
        $police = DB::table('police_users')->where('id', $request->police_id)->first();
        if (!$police) {

            return redirect()->back()->with('error', 'सदर पोलीस वापरकर्ता आढळला नाही.');
        }

        // ✅ Role-based access
        $authorized = false;
        switch ($user['designation_type']) {
            case 'Police':
                if ($police->id == $user['id']) $authorized = true;
                break;
            case 'Leave_Department':
                if ($police->id == $user['id']) $authorized = true;
                break;
            case 'Station_Head':
                if ($police->police_station_id == $user['police_station_id']) $authorized = true;
                break;
            case 'Head_Person':
                if ($police->district_id == $user['district_id']) $authorized = true;
                break;
            case 'Sewapustika_Department':
                if ($police->district_id == $user['district_id']) $authorized = true;
                break;
            case 'Admin':
                $authorized = true;
                break;
        }

        if (!$authorized) {

            return redirect()->back()->with('error', 'आपल्याला ही क्रिया करण्याची परवानगी नाही.');
        }

        try {
            // Handle the file
            $file = $request->file('sewa_pustika_file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = now()->format('Ymd_His') . '_' . Str::slug($originalName) . '.' . $extension;

            $destinationPath = base_path('uploads/sewapustika');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $uniqueFileName);


            // Insert into DB
            $inserted = DB::table('sewa_pustikas')->insert([
                'police_id'           => $request->police_id,
                'district_id'         => $request->district_id,
                'station_id'          => $request->city_id,
                'sewa_pustika_status' => 'Uploaded',
                'sewapusticapath'     => $uniqueFileName,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            if ($inserted) {

                return redirect()->back()->with('success', 'सेवा पुस्तिका यशस्वीरित्या अपलोड केली गेली.');
            } else {

                return redirect()->back()->with('error', 'सेवा पुस्तिका जतन करताना त्रुटी आली.');
            }
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'सेवा पुस्तिका जतन करताना त्रुटी आली: ' . $e->getMessage());
        }
    }



    public function view($filename)
    {
        // Root-level path (not public)
        $path = base_path('uploads/sewapustika/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'File not found.');
        }

        // Optional: check MIME type dynamically
        $mime = File::mimeType($path);

        return response()->file($path, [
            'Content-Type' => $mime ?? 'application/pdf'
        ]);
    }

    public function search(Request $request)
    {
        try {
            // 1️⃣ Check logged-in user
            $user = Session::get('user');
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please login again.'
                ], 401);
            }

            $perPage = 10;
            $keyword = $request->keyword;
            $station = $request->designation;
            $status  = $request->status; // approved, rejected, pending, uploaded, all

            // 2️⃣ Latest sewa_pustika per police user
            $latestPustika = DB::table('sewa_pustikas as sp')
                ->select('sp.id', 'sp.police_id', 'sp.sewa_pustika_status', 'sp.sewapusticapath')
                ->whereIn('sp.id', function ($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('sewa_pustikas')
                        ->groupBy('police_id');
                });

            // 3️⃣ Base query
            $query = DB::table('police_users as t4')
                ->leftJoin('districts as t2', 't4.district_id', '=', 't2.id')
                ->leftJoin('states as t1', 't2.state_id', '=', 't1.id')
                ->leftJoin('cities as t3', 't4.city_id', '=', 't3.id')
                ->leftJoin('police_stations as t6', 't4.police_station_id', '=', 't6.id')
                ->leftJoinSub($latestPustika, 't5', function ($join) {
                    $join->on('t4.id', '=', 't5.police_id');
                })
                ->leftJoin('sewapushtika_review AS t7', 't5.id', '=', 't7.sewapustika_id')
                ->select(
                    't1.state_name',
                    't1.id as state_id',
                    't2.id as district_id',
                    't2.district_name',
                    't3.id as city_id',
                    't3.city_name',
                    't4.id as police_user_id',
                    't4.police_name',
                    't4.buckle_number',
                    't4.designation_type',
                    't6.name as police_station_name',
                    't5.sewa_pustika_status',
                    't5.id AS sewapustika_id',
                    't5.sewapusticapath',
                    't4.post',
                    't4.mobile',
                    't7.remark',
                    DB::raw('COALESCE(t7.review_status, "pending") AS review_status'),
                    DB::raw('CASE
                    WHEN t5.id IS NULL AND t7.id IS NULL THEN "pending"
                    WHEN t5.id IS NOT NULL AND t7.id IS NULL THEN "uploaded"
                    ELSE COALESCE(LOWER(t7.review_status), "pending")
                END AS custom_status')
                )
                ->where('t4.is_delete', 'No');

            // 4️⃣ Filter by station (if provided)
            if (!empty($station)) {
                $query->where('t6.name', $station);
            } else {
                // Role-based filtering
                switch ($user['designation_type']) {
                    case 'Police':
                        $query->where('t4.id', $user['id']);
                        break;
                    case 'Leave_Department':
                        $query->where('t4.id', $user['id']);
                        break;
                    case 'Station_Head':
                        $query->where('t4.police_station_id', $user['police_station_id']);
                        break;
                    case 'Head_Person':
                    case 'Sewapustika_Department':
                        $query->where('t4.district_id', $user['district_id']);
                        break;
                    case 'Admin':
                        // Admin sees all
                        break;
                    default:
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Unauthorized'
                        ], 403);
                }
            }

            // 5️⃣ Keyword search including custom_status
            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $statusMap = [
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                        'pending'  => 'pending',
                        'uploaded' => 'uploaded',
                        'all'      => null
                    ];

                    if (isset($statusMap[strtolower($keyword)]) && strtolower($keyword) != 'all') {
                        $q->whereRaw('CASE
                        WHEN t5.id IS NULL AND t7.id IS NULL THEN "pending"
                        WHEN t5.id IS NOT NULL AND t7.id IS NULL THEN "uploaded"
                        ELSE COALESCE(LOWER(t7.review_status), "pending")
                    END = ?', [$statusMap[strtolower($keyword)]]);
                    } else {
                        $q->where('t4.police_name', 'like', "%{$keyword}%")
                            ->orWhere('t4.buckle_number', 'like', "%{$keyword}%")
                            ->orWhere('t4.post', 'like', "%{$keyword}%")
                            ->orWhere('t6.name', 'like', "%{$keyword}%")
                            ->orWhere('t2.district_name', 'like', "%{$keyword}%")
                            ->orWhere('t3.city_name', 'like', "%{$keyword}%")
                            ->orWhere('t1.state_name', 'like', "%{$keyword}%");
                    }
                });
            }

            // 6️⃣ Status filter (from cards)
            if (!empty($status) && strtolower($status) != 'all') {
                if (strtolower($status) === 'pending') {
                    $query->whereNotNull('t5.id')->whereNull('t7.id');
                } elseif (strtolower($status) === 'uploaded') {
                    $query->whereNotNull('t5.id')->whereNull('t7.review_status');
                } else { // approved / rejected
                    $query->whereRaw('LOWER(COALESCE(t7.review_status,"pending")) = ?', [strtolower($status)]);
                }
            }

            // 7️⃣ Pagination
            $polices = $query->orderBy('t4.id', 'desc')
                ->paginate($perPage)
                ->appends([
                    'keyword' => $keyword,
                    'designation' => $station,
                    'status' => $status
                ]);

            // 8️⃣ Return Blade partial for table
            return view('sewa_pustika.search_table', compact('polices'));
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }


    public function sewa_pustika_cards()
    {
        $user = Session::get('user');

        $counts = DB::table('police_users AS t4')
            ->leftJoin('sewa_pustikas AS sp', 't4.id', '=', 'sp.police_id')
            ->leftJoin('sewapushtika_review AS r', 'sp.id', '=', 'r.sewapustika_id') // updated table & column
            ->where('t4.district_id', $user['district_id'])
            ->select(
                DB::raw('COUNT(DISTINCT t4.id) AS total_police'),
                DB::raw('COUNT(DISTINCT sp.id) AS total_uploaded'),
                DB::raw('SUM(CASE WHEN r.review_status = "approved" THEN 1 ELSE 0 END) AS approved'),
                DB::raw('SUM(CASE WHEN r.review_status = "rejected" THEN 1 ELSE 0 END) AS rejected'),
                DB::raw('SUM(CASE WHEN sp.id IS NOT NULL AND r.id IS NULL THEN 1 ELSE 0 END) AS pending')
            )
            ->first();

        $stats = [
            'title'          => 'Sewa Pustika',
            'total_police'   => $counts->total_police,
            'total_uploaded' => $counts->total_uploaded,
            'approved'       => $counts->approved,
            'rejected'       => $counts->rejected,
            'pending'        => $counts->pending,
        ];

        return view('cards.sewa_pustika_cards', ['stats' => $stats]);
    }
}
