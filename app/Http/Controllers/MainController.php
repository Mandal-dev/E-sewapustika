<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{

    public function dashboard_old()
    {
        $user = Session::get('user'); // get logged in user session

        if (!$user) {
            return redirect()->back()->withErrors(['session' => 'Session expired, please login again.']);
        }

        try {
            // ========================= POLICE =========================
            if ($user['designation_type'] === 'Police') {
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
                    ->where('t4.id', $user['id'])
                    ->first();

                if (!$police) {
                    return back()->with('error', 'Police profile not found.');
                }

                return view('profile.index', compact('police'));
            }

            // ========================= STATION HEAD =========================
            elseif ($user['designation_type'] === 'Station_Head') {
                $station = DB::table('police_users')
                    ->where('id', $user['id'])
                    ->select('police_station_id')
                    ->first();

                if (!$station) {
                    return back()->with('error', 'Station not found.');
                }

                $station_id = $station->police_station_id;

                $total_police = DB::table('police_users')
                    ->where('police_station_id', $station_id)
                    ->count();

                // Count stations in the same district (not just ID = 1 always)
                $total_police_thane = DB::table('police_stations')
                    ->where('district_id', $user['district_id'] ?? 0)
                    ->count();

                $total_pustika = DB::table('sewa_pustikas')
                    ->where('station_id', $station_id)
                    ->distinct('police_id')
                    ->count('police_id');

                $total_punishments = DB::table('police_punishments')
                    ->where('station_id', $station_id)
                    ->distinct('police_id')
                    ->count('police_id');

                $total_salary_increments = DB::table('salary_increments')
                    ->where('station_id', $station_id)
                    ->distinct('police_id')
                    ->count('police_id');

                return view('Dashboard.dashboard', compact(
                    'total_police',
                    'total_pustika',
                    'total_punishments',
                    'total_salary_increments',
                    'total_police_thane'
                ));
            }

            // ========================= HEAD PERSON =========================
            elseif ($user['designation_type'] === 'Head_Person') {
                if (!isset($user['district_id'])) {
                    return back()->with('error', 'District not found in session.');
                }

                $total_police = DB::table('police_users')
                    ->where('district_id', $user['district_id'])
                    ->count();

                $total_police_thane = DB::table('police_stations')
                    ->where('district_id', $user['district_id'])
                    ->count();

                $total_pustika = DB::table('sewa_pustikas')
                    ->where('district_id', $user['district_id'])
                    ->distinct('police_id')
                    ->count('police_id');

                $total_punishments = DB::table('police_punishments')
                    ->where('district_id', $user['district_id'])
                    ->distinct('police_id')
                    ->count('police_id');

                $total_salary_increments = DB::table('salary_increments')
                    ->where('district_id', $user['district_id'])
                    ->distinct('police_id')
                    ->count('police_id');

                return view('Dashboard.dashboard', compact(
                    'total_police',
                    'total_pustika',
                    'total_punishments',
                    'total_salary_increments',
                    'total_police_thane'
                ));
            }

            // ========================= DEPARTMENT-SPECIFIC =========================
            elseif ($user['designation_type'] === 'Punishment_Department') {
                return redirect()->route('punishments.index');
            } elseif ($user['designation_type'] === 'Sewapustika_Department') {
                return redirect()->route('sewa_pustika.index');
            } elseif ($user['designation_type'] === 'Account_Department') {
                return redirect()->route('salary_increment.index');
            } elseif ($user['designation_type'] === 'Rewards_Department') {
                return redirect()->route('rewards.index');
            }

            // ========================= ADMIN / OTHERS =========================
            else {
                $total_police = DB::table('police_users')->count();
                $total_police_thane = DB::table('police_stations')->count();

                $total_pustika = DB::table('sewa_pustikas')
                    ->distinct('police_id')
                    ->count('police_id');

                $total_punishments = DB::table('police_punishments')
                    ->distinct('police_id')
                    ->count('police_id');

                $total_salary_increments = DB::table('salary_increments')
                    ->distinct('police_id')
                    ->count('police_id');

                return view('Dashboard.dashboard', compact(
                    'total_police',
                    'total_pustika',
                    'total_punishments',
                    'total_salary_increments',
                    'total_police_thane'
                ));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
public function dashboard()
{
    $user = Session::get('user');
    if (!$user) {
        return redirect('/login')->with('error', 'Session expired. Please login again.');
    }

    $districtId = $user['district_id'];

    // Salary Increment
    $salary = DB::table('police_users AS t4')
        ->leftJoin('salary_increments AS s', 't4.id', '=', 's.police_id')
        ->leftJoin('salary_reviews AS r', 's.id', '=', 'r.salary_id')
        ->where('t4.district_id', $districtId)
        ->select(
            DB::raw('COUNT(DISTINCT t4.id) AS total_police'),
            DB::raw('COUNT(DISTINCT s.id) AS total_uploaded'),
            DB::raw('SUM(CASE WHEN r.review_status = "approved" THEN 1 ELSE 0 END) AS approved'),
            DB::raw('SUM(CASE WHEN r.review_status = "rejected" THEN 1 ELSE 0 END) AS rejected'),
            DB::raw('SUM(CASE WHEN s.id IS NOT NULL AND r.id IS NULL THEN 1 ELSE 0 END) AS pending')
        )
        ->first();

    // Reward
    $reward = DB::table('police_users AS t4')
        ->leftJoin('police_rewards AS t5', 't4.id', '=', 't5.police_id')
        ->leftJoin('reward_reviews AS t6', 't5.id', '=', 't6.reward_id')
        ->where('t4.district_id', $districtId)
        ->select(
            DB::raw('COUNT(DISTINCT t4.id) AS total_police'),
            DB::raw('COUNT(DISTINCT t5.id) AS total_uploaded'),
            DB::raw('SUM(CASE WHEN t6.review_status = "Approved" THEN 1 ELSE 0 END) AS approved'),
            DB::raw('SUM(CASE WHEN t6.review_status = "Rejected" THEN 1 ELSE 0 END) AS rejected'),
            DB::raw('SUM(CASE WHEN t5.id IS NOT NULL AND t6.id IS NULL THEN 1 ELSE 0 END) AS pending')
        )
        ->first();

    // Punishments
    $punishment = DB::table('police_users AS t4')
        ->leftJoin('police_punishments AS p', 't4.id', '=', 'p.police_id')
        ->leftJoin('punishment_reviews AS r', 'p.id', '=', 'r.punishment_id')
        ->where('t4.district_id', $districtId)
        ->select(
            DB::raw('COUNT(DISTINCT t4.id) AS total_police'),
            DB::raw('COUNT(DISTINCT p.id) AS total_uploaded'),
            DB::raw('SUM(CASE WHEN r.review_status = "approved" THEN 1 ELSE 0 END) AS approved'),
            DB::raw('SUM(CASE WHEN r.review_status = "rejected" THEN 1 ELSE 0 END) AS rejected'),
            DB::raw('SUM(CASE WHEN p.id IS NOT NULL AND r.id IS NULL THEN 1 ELSE 0 END) AS pending')
        )
        ->first();

    // Sewa Pustika
    $sewaPustika = DB::table('police_users AS t4')
        ->leftJoin('sewa_pustikas AS sp', 't4.id', '=', 'sp.police_id')
        ->leftJoin('sewapushtika_review AS r', 'sp.id', '=', 'r.sewapustika_id')
        ->where('t4.district_id', $districtId)
        ->select(
            DB::raw('COUNT(DISTINCT t4.id) AS total_police'),
            DB::raw('COUNT(DISTINCT sp.id) AS total_uploaded'),
            DB::raw('SUM(CASE WHEN r.review_status = "approved" THEN 1 ELSE 0 END) AS approved'),
            DB::raw('SUM(CASE WHEN r.review_status = "rejected" THEN 1 ELSE 0 END) AS rejected'),
            DB::raw('SUM(CASE WHEN sp.id IS NOT NULL AND r.id IS NULL THEN 1 ELSE 0 END) AS pending')
        )
        ->first();

    $cards = [
        [
            'title' => 'Salary Increment',
            'total_police' => $salary->total_police,
            'total_uploaded' => $salary->total_uploaded,
            'approved' => $salary->approved,
            'rejected' => $salary->rejected,
            'pending' => $salary->pending
        ],
        [
            'title' => 'Rewards',
            'total_police' => $reward->total_police,
            'total_uploaded' => $reward->total_uploaded,
            'approved' => $reward->approved,
            'rejected' => $reward->rejected,
            'pending' => $reward->pending
        ],
        [
            'title' => 'Punishments',
            'total_police' => $punishment->total_police,
            'total_uploaded' => $punishment->total_uploaded,
            'approved' => $punishment->approved,
            'rejected' => $punishment->rejected,
            'pending' => $punishment->pending
        ],
        [
            'title' => 'Sewa Pustika',
            'total_police' => $sewaPustika->total_police,
            'total_uploaded' => $sewaPustika->total_uploaded,
            'approved' => $sewaPustika->approved,
            'rejected' => $sewaPustika->rejected,
            'pending' => $sewaPustika->pending
        ]
    ];

    return view('Dashboard.dashboard', compact('cards'));
}



    public function newDashboard()
    {
        return view('Dashboard.manu');
    }

    public function getStates($countryId)
    {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $userId = $user['id'];

        if ($user['designation_type'] === 'Police') {
            return response()->json(['error' => 'Access denied.'], 403);
        } elseif ($user['designation_type'] === 'Station_Head') {
            return DB::table('police_users')
                ->join('states', 'police_users.state_id', '=', 'states.id')
                ->join('districts', 'states.id', '=', 'districts.state_id')
                ->where('police_users.id', $userId)
                ->where('districts.id', $user['district_id'])
                ->where('states.country_id', $countryId)
                ->where('states.is_delete', 'No')

                ->select('states.id', 'states.state_name')
                ->get();
        } elseif ($user['designation_type'] === 'Head_Person') {
            return DB::table('police_users')
                ->join('states', 'police_users.state_id', '=', 'states.id')
                ->join('districts', 'states.id', '=', 'districts.state_id')
                ->where('police_users.id', $userId)
                ->where('districts.id', $user['district_id'])
                ->where('states.country_id', $countryId)
                ->where('states.is_delete', 'No')

                ->select('states.id', 'states.state_name')
                ->get();
        } elseif ($user['designation_type'] === 'Admin') {
            return DB::table('states')
                ->where('country_id', $countryId)
                ->where('is_delete', 'No')

                ->select('id', 'state_name')
                ->get();
        }

        return response()->json(['error' => 'Invalid user'], 400);
    }

    public function getDistricts($stateId)
    {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $userId = $user['id'];

        if ($user['designation_type'] === 'Police') {
            return response()->json(['error' => 'Access denied.'], 403);
        } elseif ($user['designation_type'] === 'Station_Head') {
            return DB::table('districts')
                ->where('id', $user['district_id'])
                ->where('state_id', $stateId)
                ->where('is_delete', 'No')
                ->where('status', 'Active')
                ->select('id', 'district_name')
                ->get();
        } elseif ($user['designation_type'] === 'Head_Person') {
            return DB::table('districts')
                ->where('id', $user['district_id'])
                ->where('state_id', $stateId)
                ->where('is_delete', 'No')
                ->where('status', 'Active')
                ->select('id', 'district_name')
                ->get();
        } elseif ($user['designation_type'] === 'Admin') {
            return DB::table('districts')
                ->where('state_id', $stateId)
                ->where('is_delete', 'No')
                ->where('status', 'Active')
                ->select('id', 'district_name')
                ->get();
        }

        return response()->json(['error' => 'Invalid user'], 400);
    }

    public function getCities($districtId)
    {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($user['designation_type'] === 'Police') {
            return response()->json(['error' => 'Access denied.'], 403);
        } elseif ($user['designation_type'] === 'Station_Head' || $user['designation_type'] === 'Head_Person') {
            return DB::table('cities')
                ->where('district_id', $user['district_id'])
                ->where('is_delete', 'No')
                ->where('status', 'Active')
                ->select('id', 'city_name')
                ->get();
        } elseif ($user['designation_type'] === 'Admin') {
            return DB::table('cities')
                ->where('district_id', $districtId)
                ->where('is_delete', 'No')
                ->where('status', 'Active')
                ->select('id', 'city_name')
                ->get();
        }

        return response()->json(['error' => 'Invalid user'], 400);
    }

    public function getStations($cityId)
    {
        return DB::table('police_stations')
            ->where('city_id', $cityId)
            ->where('is_delete', 'No')
            ->where('status', 'Active')
            ->get();
    }

    public function getStationsByCity($cityId)
    {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($user['designation_type'] === 'Police') {
            return response()->json(['error' => 'Access denied.'], 403);
        } elseif ($user['designation_type'] === 'Station_Head' || $user['designation_type'] === 'Head_Person') {
            return DB::table('police_stations')
                ->where('district_id', $user['district_id'])
                ->where('city_id', $cityId)
                ->where('is_delete', 'No')
                ->where('status', 'Active')
                ->select('id', 'name')
                ->get();
        } elseif ($user['designation_type'] === 'Admin') {
            return DB::table('police_stations')
                ->where('city_id', $cityId)
                ->where('is_delete', 'No')
                ->where('status', 'Active')
                ->select('id', 'name')
                ->get();
        }

        return response()->json(['error' => 'Invalid user'], 400);
    }
    public function getStationsByUser()
    {
        $user = Session::get('user');

        if (!$user) {
            return response()->json([]); // return empty if no user
        }

        $query = DB::table('police_stations')
            ->where('is_delete', 'No')
            ->where('status', 'Active');

        switch ($user['designation_type']) {
            case 'Station_Head':
            case 'Police':
                $stations = $query->where('police_station_id', $user['police_station_id'] ?? 0)
                    ->pluck('name');
                break;

            case 'Head_Person':
                $stations = $query->where('district_id', $user['district_id'] ?? 0)
                    ->pluck('name');
                break;

            case 'Admin':
                $stations = $query->pluck('name');
                break;

            default:
                $stations = collect();
        }

        return response()->json($stations);
    }
}
