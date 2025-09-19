<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;


class rewardsController extends Controller
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
                ->leftJoin('states AS t1', 't2.state_id', '=', 't1.id')
                ->leftJoin('cities AS t3', 't4.city_id', '=', 't3.id')
                ->leftJoin('police_rewards AS t5', 't4.id', '=', 't5.police_id')
                ->leftJoin('reward_reviews AS t6', 't5.id', '=', 't6.reward_id')
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
                    't5.reward_given_date',
                    't5.reason',
                    't6.reject_reason',
                    't5.reward_type',
                    't5.rewards_documents',
                    't5.id AS reward_id',
                    DB::raw('COALESCE(t6.review_status, "Pending") AS reward_status')
                );

            // ✅ Role-based filter
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

                case 'Admin':
                    // no filter
                    break;

                default:
                    return redirect('/')->with('error', 'Unauthorized access.');
            }

            // ✅ Proper pagination
            $polices = $query->orderBy('t4.id', 'desc')->paginate($perPage);

            return view('rewards.index', compact('polices'));

        } catch (\Exception $e) {
            // ✅ Return empty paginator (instead of collect())
            $emptyPaginator = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            return view('rewards.index', [
                'polices' => $emptyPaginator,
                'error'   => $e->getMessage()
            ]);
        }
    }


    public function aproveReward($rewardId)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/');
        }

        // Only Head Person can access this
        if ($user['designation_type'] !== 'Head_Person') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $police = DB::table('police_users AS t4')
            ->join('districts AS t2', 't4.district_id', '=', 't2.id')
            ->join('states AS t1', 't2.state_id', '=', 't1.id')
            ->join('cities AS t3', 't4.city_id', '=', 't3.id')
            ->join('police_rewards AS t6', 't4.id', '=', 't6.police_id')
            ->select(
                't4.id AS police_user_id',
                't4.police_name',
                't4.buckle_number',
                't4.designation_type AS role',
                't1.id AS state_id',
                't1.state_name',
                't2.id AS district_id',
                't2.district_name',
                't3.id AS city_id',
                't3.city_name',
                't3.status AS city_status',
                't6.reason',
                't6.reward_type',
                't6.reward_given_date',
                't6.rewards_documents',
                't6.id AS id'
            )
            ->where('t4.is_delete', 'No')
            ->where('t4.district_id', $user['district_id']) // Head Person’s district
            ->where('t6.id', $rewardId) // Reward filter
            ->orderBy('t4.id', 'desc')
            ->first();

        if (!$police) {
            return redirect()->back()->with('error', 'No reward found or not authorized.');
        }

        return view('rewards.reward_aprove', compact('police'));
    }


    public function policeRewardAdd($id)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/');
        }

        $query = DB::table('police_users AS t4')
            ->join('districts AS t2', 't4.district_id', '=', 't2.id')
            ->join('states AS t1', 't2.state_id', '=', 't1.id')
            ->join('cities AS t3', 't4.city_id', '=', 't3.id')
            ->select(
                't4.id AS police_user_id',
                't4.police_name',
                't4.buckle_number',
                't4.designation_type AS role',   // 👈 role
                't1.id AS state_id',
                't1.state_name',
                't2.id AS district_id',
                't2.district_name',
                't3.id AS city_id',
                't3.city_name',
                't3.status AS city_status'
            )
            ->where('t4.is_delete', 'No');

        // 👇 Apply role-based filter
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

            case 'Admin':
                // no extra filter
                break;

            default:
                return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $police = $query->where('t4.id', $id)
            ->orderBy('t4.id', 'desc')
            ->first();

        return view('rewards.add', compact('police'));
    }


    public function store(Request $request)
    {
        Log::info('Reward store method hit');

        // Log input data (excluding file)
        Log::info('Request input:', $request->except(['rewards_documents']));

        // Validation
        $request->validate([
            'police_id'          => 'required|integer',
            'district_id'        => 'required|integer',
            'station_id'         => 'nullable|integer',
            'reward_given_date'  => 'required|date',
            'reward_type'        => 'required|string',
            'reason'             => 'nullable|string',
            'rewards_documents'  => 'required|file|mimes:pdf|max:5120', // 5 MB
        ]);

        try {
            // Handle the file
            $file = $request->file('rewards_documents');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = now()->format('Ymd_His') . '_' . Str::slug($originalName) . '.' . $extension;

            // Save to root-level /uploads/rewards (NOT public)
            $destinationPath = base_path('uploads/rewards');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $uniqueFileName);
            Log::info('Reward PDF stored at: ' . $destinationPath . '/' . $uniqueFileName);

            // Insert into DB
            DB::table('police_rewards')->insert([
                'police_id'          => $request->police_id,
                'district_id'        => $request->district_id,
                'station_id'         => $request->station_id,
                'reward_given_date'  => $request->reward_given_date,
                'reward_type'        => $request->reward_type,
                'reason'             => $request->reason,
                'rewards_documents'  => $uniqueFileName,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            return redirect()->back()->with('success', 'बक्षीस दस्तऐवज यशस्वीरित्या अपलोड केला गेला.');
        } catch (\Exception $e) {
            Log::error('Error storing Reward Document', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'बक्षीस जतन करताना त्रुटी आली: ' . $e->getMessage());
        }
    }


    public function view($filename)
    {
        // Root-level path (not public)
        $path = base_path('uploads/rewards/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'File not found.');
        }

        // Optional: check MIME type dynamically
        $mime = File::mimeType($path);

        return response()->file($path, [
            'Content-Type' => $mime ?? 'application/pdf'
        ]);
    }

    public function aproveRewardStore(Request $request)
    {

        // Get user from session
        $user = Session::get('user');
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthenticated');
        }

        // Only Head_Person can approve/reject
        if ($user['designation_type'] !== 'Head_Person') {
            return redirect()->back()->with('error', 'Access denied');
        }

        // Validate request
        $request->validate([
            'reward_id'    => 'required|integer|exists:police_rewards,id',
            'status'       => 'required|in:Approved,Rejected',
            'gadget_no'    => 'required_if:status,Approved',
            'remark'       => 'required_if:status,Rejected',
        ]);

        // Check if reward is already approved
        $existingApproved = DB::table('reward_reviews')
            ->where('reward_id', $request->reward_id)
            ->where('review_status', 'approved')
            ->first();

        if ($existingApproved && $request->status === 'Approved') {
            return redirect()->back()->with('error', 'This reward has already been approved. You cannot approve it again.');
        }

        // Prepare data for insertion
        $data = [
            'reward_id'     => $request->reward_id,
            'reviewed_by'   => $user['id'],
            'review_status' => strtolower($request->status),
            'gadget_number' => $request->status === 'Approved' ? $request->gadget_no : null,
            'reject_reason' => $request->status === 'Rejected' ? $request->remark : null,
            'created_at'    => now(),
        ];

        // Log for debugging
        Log::info('Reward Review Data: ', $data);

        try {
            $id = DB::table('reward_reviews')->insertGetId($data);
            Log::info('Reward Review Inserted ID: ' . $id);

            return redirect()->back()->with('success', 'Reward review stored successfully.');
        } catch (\Exception $e) {
            Log::error('Reward Review Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to store reward review.');
        }
    }

    public function search(Request $request)
{
    try {
        $user = Session::get('user');
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please login again.'
            ], 401);
        }

        $perPage = 10;
        $keyword = $request->get('keyword');
        $designation = $request->get('designation');

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
            ->leftJoin('states AS t1', 't2.state_id', '=', 't1.id')
            ->leftJoin('cities AS t3', 't4.city_id', '=', 't3.id')
            ->leftJoin('police_rewards AS t5', 't4.id', '=', 't5.police_id')
            ->leftJoin('reward_reviews AS t6', 't5.id', '=', 't6.reward_id')
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
                't5.reward_given_date',
                't5.reason',
                't6.reject_reason',
                't5.reward_type',
                't5.rewards_documents',
                't5.id AS reward_id',
                DB::raw('COALESCE(t6.review_status, "Pending") AS reward_status')
            );

        // ✅ Role-based filter
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

            case 'Admin':
                // no filter
                break;

            default:
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.'
                ], 403);
        }

        // ✅ Apply search filters
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('t4.police_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('t4.buckle_number', 'LIKE', "%{$keyword}%")
                  ->orWhere('t2.district_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('t3.city_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('t1.state_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('t5.reason', 'LIKE', "%{$keyword}%");
            });
        }

        if (!empty($designation)) {
            $query->where('t4.designation_type', $designation);
        }

        $polices = $query->orderBy('t4.id', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $polices
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

}
