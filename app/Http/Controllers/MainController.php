<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{
    public function dashboard()
    {
        $user = Session::get('user'); // get logged in user session

        if (!$user) {
            return redirect()->back()->withErrors(['session' => 'Session expired, please login again.']);
        }

        try {
            if ($user['designation_type'] === 'Police') {
                // Police → redirect to their profile page
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
            } elseif ($user['designation_type'] === 'Station_Head') {
                // Station Head → show limited totals
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

                $total_police_thane = DB::table('police_stations')
                    ->where('id', $station_id)
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
            } elseif ($user['designation_type'] === 'Head_Person') {
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
            } else {
                // Admin / Other roles → show full totals
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
