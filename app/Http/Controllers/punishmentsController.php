<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class punishmentsController extends Controller
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
                         $q->where('t2.is_delete', 'No')->orWhereNull('t2.is_delete');
                     })
                     ->where(function ($q) {
                         $q->where('t2.status', 'Active')->orWhereNull('t2.status');
                     });
            })
            ->leftJoin('states AS t1', 't2.state_id', '=', 't1.id')
            ->leftJoin('cities AS t3', 't4.city_id', '=', 't3.id')
            ->leftJoin('police_punishments AS t5', 't4.id', '=', 't5.police_id')
            ->leftJoin('punishment_reviews AS t6', 't5.id', '=', 't6.punishment_id')
            ->select(
                't1.state_name',
                't1.id AS state_id',
                't2.id AS district_id',
                't2.district_name',
                't3.id AS city_id',
                't3.city_name',
                't3.status AS city_status',
                't4.id AS police_user_id',
                't4.police_name',
                't4.buckle_number',
                't4.designation_type AS role',
                't5.punishment_given_date',
                't5.reason',
                't5.id AS punishment_id',

                't5.punishment_type',
                't5.punishment_documents',
                't6.reviewed_by',
                't6.gadget_number',
                't6.reject_reason',
                DB::raw('COALESCE(t6.review_status, "Pending") AS punishment_status')
            );

        // Role-based filter
        switch ($user['designation_type']) {
            case 'Police':
                $query->where('t4.id', $user['id']);
                break;

            case 'Station_Head':
                $myStationId = DB::table('police_users')
                    ->where('id', $user['id'])
                    ->value('police_station_id');
                $query->where('t4.police_station_id', $myStationId);
                break;

            case 'Head_Person':
            case 'Punishment_Department':
                $query->where('t4.district_id', $user['district_id']);
                break;

            case 'Admin':
                // no extra filter
                break;

            default:
                return redirect('/')->with('error', 'Unauthorized access.');
        }

        // Pagination
        $polices = $query->orderBy('t4.id', 'desc')->paginate($perPage);

        return view('punishments.index', compact('polices'));
    } catch (\Exception $e) {
        $emptyPaginator = new LengthAwarePaginator([], 0, 10, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('punishments.index', [
            'polices' => $emptyPaginator,
            'error'   => $e->getMessage()
        ]);
    }
}


    public function policePunishmentAdd($id)
    {
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
                't3.status AS city_status',

            )
            ->where('t4.is_delete', 'No')
            ->where('t4.id', $id)
            ->orderBy('t4.id', 'desc')
            ->first();

        return view('Punishments.edit', compact('police'));
    }


    public function store(Request $request)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/');
        }

        // Check designation
        $designation = $user['designation_type'] ?? '';
        if (!in_array($designation, ['Head_Person', 'Punishment_Department'])) {
            return redirect()->back()->with('error', 'आपल्याकडे शिक्षण जतन करण्याची परवानगी नाही.');
        }

        Log::info('Punishment store method hit');

        // Log input data (excluding file)
        Log::info('Request input:', $request->except(['punishment_documents']));

        // Validation
        $request->validate([
            'police_id'              => 'required|integer',
            'district_id'            => 'required|integer',
            'station_id'             => 'nullable|integer',
            'punishment_given_date'  => 'required|date',
            'punishment_type'        => 'required|string',
            'reason'                 => 'nullable|string',
            'punishment_documents'   => 'required|file|mimes:pdf|max:5120', // 5 MB
        ]);

        try {
            // Handle the file
            $file = $request->file('punishment_documents');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = now()->format('Ymd_His') . '_' . Str::slug($originalName) . '.' . $extension;

            $destinationPath = base_path('uploads/punishments');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $uniqueFileName);
            Log::info('Punishment PDF stored at: ' . $destinationPath . '/' . $uniqueFileName);

            DB::table('police_punishments')->insert([
                'police_id'             => $request->police_id,
                'district_id'           => $request->district_id,
                'station_id'            => $request->station_id,
                'punishment_given_date' => $request->punishment_given_date,
                'punishment_type'       => $request->punishment_type,
                'reason'                => $request->reason,
                'punishment_documents'  => $uniqueFileName,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            return redirect()->back()->with('success', 'शिक्षा दस्तऐवज यशस्वीरित्या अपलोड केला गेला.');
        } catch (\Exception $e) {
            Log::error('Error storing Punishment Document', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'शिक्षा जतन करताना त्रुटी आली: ' . $e->getMessage());
        }
    }


    public function view($filename)
    {
        // Root-level path (not public)
        $path = base_path('uploads/punishments/' . $filename);

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
            $user = Session::get('user');
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access. Please login again.'
                ], 401);
            }

            $perPage = 10;
            $keyword = $request->get('keyword');
            $designation = $request->get('designation');

            // latest punishment subquery
            $latestPustika = DB::table('police_punishments')
                ->select('id', 'police_id', 'punishment_documents', 'punishment_given_date', 'punishment_type', 'reason')
                ->whereRaw('id IN (SELECT MAX(id) FROM police_punishments GROUP BY police_id)');

            // base query
            $query = DB::table('police_users AS t4')
                ->join('districts AS t2', 't4.district_id', '=', 't2.id')
                ->join('states AS t1', 't2.state_id', '=', 't1.id')
                ->join('cities AS t3', 't4.city_id', '=', 't3.id')
                ->leftJoinSub($latestPustika, 't5', function ($join) {
                    $join->on('t4.id', '=', 't5.police_id');
                })
                ->select(
                    't1.state_name',
                    't1.id AS state_id',
                    't2.id AS district_id',
                    't2.district_name',
                    't3.id AS city_id',
                    't3.city_name',
                    't3.status AS city_status',
                    't4.id AS police_user_id',
                    't4.police_name',
                    't4.buckle_number',
                    't4.designation_type AS role',
                    't5.punishment_given_date',
                    't5.reason',
                    't5.punishment_type',
                    't5.punishment_documents'
                )
                ->where('t2.is_delete', 'No')
                ->where('t2.status', 'Active');

            // 🔎 keyword search
            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('t4.police_name', 'like', "%{$keyword}%")
                        ->orWhere('t4.buckle_number', 'like', "%{$keyword}%")
                        ->orWhere('t5.reason', 'like', "%{$keyword}%")
                        ->orWhere('t5.punishment_type', 'like', "%{$keyword}%");
                });
            }

            // 🔎 designation filter
            if (!empty($designation)) {
                $query->where('t4.designation_type', $designation);
            }

            // designation-based restrictions
            switch ($user['designation_type']) {
                case 'Police':
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

                case 'Punishment_Department':
                    $query->where('t4.district_id', $user['district_id']);
                    break;

                case 'Admin':
                    // no extra filter
                    break;

                default:
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unauthorized access.'
                    ], 403);
            }

            // pagination
            $results = $query->orderBy('t4.id', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. ' . $e->getMessage()
            ], 500);
        }
    }

     /**
     * Show punishment approval details
     */
    public function show($punishmentId)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/')->with('error', 'Session expired. Please login again.');
            }

            $punishment = DB::table('police_punishments AS p')
                ->leftJoin('punishment_reviews AS r', 'p.id', '=', 'r.punishment_id')
                ->leftJoin('police_users AS pu', 'p.police_id', '=', 'pu.id')
                ->leftJoin('districts AS d', 'pu.district_id', '=', 'd.id')
                ->leftJoin('states AS s', 'd.state_id', '=', 's.id')
                ->leftJoin('cities AS c', 'pu.city_id', '=', 'c.id')
                ->select(
                    'p.id AS punishment_id',
                    'p.police_id',
                    'p.punishment_type',
                    'p.punishment_documents',
                    'p.punishment_given_date',
                    'p.reason',

                    // Review table
                    'r.id AS review_id',
                    DB::raw('COALESCE(r.review_status, "Pending") AS review_status'),
                    'r.reject_reason',
                    'r.gadget_number',
                    'r.created_at AS review_date',

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
                ->where('p.id', $punishmentId)
                ->first();

            if (!$punishment) {
                return redirect()->back()->with('error', 'Punishment record not found.');
            }

            return view('Punishments.aprove', compact('punishment'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Store punishment approval
     */
public function approvePunishmentStore(Request $request)
{
    $user = Session::get('user');

    if (!$user) {
        return redirect()->back()->with('error', 'Unauthenticated. Please login.');
    }

    if ($user['designation_type'] !== 'Head_Person') {
        return redirect()->back()->with('error', 'Access denied. Only Head_Person can approve/reject.');
    }

    // Conditional validation based on status
    $request->validate([
        'punishment_id' => 'required|integer|exists:police_punishments,id',
        'status' => 'required|in:Approved,Rejected',
        'remark' => 'required|string|max:1000', // This will be used for both cases
    ]);

    try {
        // Prepare data array
        $data = [
            'punishment_id' => $request->punishment_id,
            'reviewed_by' => $user['id'],
            'review_status' => strtolower($request->status),
            'gadget_number' => $request->status === 'Approved' ? $request->remark : null,
            'reject_reason' => $request->status === 'Rejected' ? $request->remark : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Debug: check data before insert (remove this in production)
        Log::info('Punishment Review Data:', $data);

        // Insert into database - make sure table name is correct
        DB::table('punishment_reviews')->insert($data);

        return redirect()->back()->with('success', 'Punishment review stored successfully.');

    } catch (\Exception $e) {
        Log::error('Punishment Review Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to store punishment review: ' . $e->getMessage());
    }
}
    /**
     * Punishment summary cards (for AJAX)
     */
    public function punishment_cards()
    {
        $user = Session::get('user');

        $counts = DB::table('police_users AS t4')
            ->leftJoin('police_punishments AS p', 't4.id', '=', 'p.police_id')
            ->leftJoin('punishment_reviews AS r', 'p.id', '=', 'r.punishment_id')
            ->where('t4.district_id', $user['district_id'])
            ->select(
                DB::raw('COUNT( t4.id) AS total_police'),
                DB::raw('COUNT( p.id) AS total_uploaded'),
                DB::raw('SUM(CASE WHEN r.review_status = "approved" THEN 1 ELSE 0 END) AS approved'),
                DB::raw('SUM(CASE WHEN r.review_status = "rejected" THEN 1 ELSE 0 END) AS rejected'),
                DB::raw('SUM(CASE WHEN p.id IS NOT NULL AND r.id IS NULL THEN 1 ELSE 0 END) AS pending')
            )
            ->first();

        $stats = [
            'title'         => 'Punishments',
            'total_police'  => $counts->total_police,
            'total_uploaded'=> $counts->total_uploaded,
            'approved'      => $counts->approved,
            'rejected'      => $counts->rejected,
            'pending'       => $counts->pending
        ];

        return view('cards.punishment_cards', compact('stats'));
    }
}
