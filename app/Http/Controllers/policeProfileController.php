<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class policeProfileController extends Controller
{
public function index($id)
{
    Log::info('policeProfileController@index called', ['user_id' => Session::get('user.id') ?? null, 'police_id' => $id]);

    try {
        $user = Session::get('user');

        if (!$user) {
            Log::warning('Access denied: user not logged in');
            return redirect('/')->with('error', 'Please login first.');
        }

        Log::info('User session found', ['user' => $user]);

        // Base query
        $query = DB::table('police_users AS t4')
            ->leftJoin('districts AS t2', 't4.district_id', '=', 't2.id')
            ->leftJoin('states AS t1', 't2.state_id', '=', 't1.id')
            ->leftJoin('cities AS t3', 't4.city_id', '=', 't3.id')
            ->leftJoin('police_stations AS t6', 't4.police_station_id', '=', 't6.id')
            ->select(
                't4.id AS police_user_id',
                't4.police_name',
                't4.mobile',

                't4.buckle_number',
                't1.id AS state_id',
                't1.state_name',
                't2.id AS district_id',
                't2.district_name',
                't6.id AS station_id',
                't6.name AS police_station_name',
                't3.id AS city_id',
                't3.city_name',
                't3.status AS city_status'
            )
            ->where('t4.is_delete', 'No');


        Log::info('Base query prepared');

        // Role-based access
        switch ($user['designation_type']) {
            case 'Police':
                $query->where('t4.id', $user['id']);
                Log::info('Role Police: viewing own profile', ['user_id' => $user['id']]);
                break;

            case 'Station_Head':
                $myStationId = DB::table('police_users')
                    ->where('id', $user['id'])
                    ->value('police_station_id');
                $query->where('t4.police_station_id', $myStationId);
                Log::info('Role Station_Head: viewing station police', ['station_id' => $myStationId]);
                break;

            case 'Head_Person':
                $query->where('t4.district_id', $user['district_id']);
                Log::info('Role Head_Person: viewing district police', ['district_id' => $user['district_id']]);
                break;

            case 'Admin':
                Log::info('Role Admin: viewing all police records');
                break;

            default:
                Log::warning('Unauthorized access attempt', ['user' => $user]);
                return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Fetch single record
        $police = $query->where('t4.id', $id)
                        ->orderBy('t4.id', 'desc')
                        ->first();

        if (!$police) {
            Log::warning('Police record not found or access denied', ['police_id' => $id, 'user' => $user]);
            return redirect()->back()->with('error', 'Police record not found or access denied.');
        }

        Log::info('Police record fetched successfully', ['police' => $police]);

        return view('profile.index', compact('police'));

    } catch (\Exception $e) {
        Log::error('Error in policeProfileController@index', [
            'error' => $e->getMessage(),
            'user' => $user ?? null,
            'police_id' => $id
        ]);
        return back()->with('error', 'Unable to load profile data.');
    }
}



    public function edit($id)
    {
        try {
            return view('police_profile.edit', compact('id'));
        } catch (\Exception $e) {
            Log::error('Error in policeProfileController@edit: ' . $e->getMessage());
            return back()->with('error', 'Unable to load edit form.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Logic to update the police profile
            // Validate and save the updated data
            return redirect()->route('police_profile.index')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in policeProfileController@update: ' . $e->getMessage());
            return back()->with('error', 'Unable to update profile.');
        }
    }

    public function policeSewaPustika($id)
    {
        try {
            $latestPustika = DB::table('sewa_pustikas')
                ->select('id', 'police_id', 'sewa_pustika_status', 'sewapusticapath')
                ->whereRaw('id IN (SELECT MAX(id) FROM sewa_pustikas GROUP BY police_id)');

            $polices = DB::table('police_users AS t4')
                ->leftjoin('districts AS t2', 't4.district_id', '=', 't2.id')
                ->leftjoin('states AS t1', 't2.state_id', '=', 't1.id')
                ->leftjoin('cities AS t3', 't4.city_id', '=', 't3.id')
                ->leftJoin('police_stations AS t6', 't4.police_station_id', '=', 't6.id')
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
                    't6.name AS police_station_name',
                    't5.sewa_pustika_status',
                    't5.sewapusticapath'
                )
                ->where('t4.id', $id)
                ->where('t2.is_delete', 'No')
                ->where('t2.status', 'Active')
                ->orderBy('t4.id', 'desc')
                ->get();

            return view('profile.single_sewa_pustika', compact('polices'));
        } catch (\Exception $e) {
            Log::error('Error in policeProfileController@policeSewaPustika: ' . $e->getMessage());
            return back()->with('error', 'Unable to fetch Sewa Pustika data.');
        }
    }

    public function punishmentHistory($id)
    {
        try {
            $punishments = DB::table('police_punishments AS pp')
                ->join('police_users AS pu', 'pp.police_id', '=', 'pu.id')
                ->select(
                    'pp.id',
                    'pp.punishment_given_date',
                    'pp.punishment_type',
                    'pp.reason',
                    'pp.punishment_documents',
                    'pu.police_name',
                    'pu.buckle_number',
                    'pu.id AS police_user_id'
                )
                ->where('pp.police_id', $id)
                ->orderBy('pp.punishment_given_date', 'desc')
                ->get();

            return view('profile.punishment_history', compact('punishments'));
        } catch (\Exception $e) {
            Log::error('Error in policeProfileController@history: ' . $e->getMessage());
            return back()->with('error', 'Unable to load punishment history.');
        }
    }

    public function rewardsHistory($id)
    {
        try {
            $punishments = DB::table('police_rewards AS pp')
                ->join('police_users AS pu', 'pp.police_id', '=', 'pu.id')
                ->select(
                    'pp.id',
                    'pp.rewards_documents',
                    'pp.reward_given_date',
                    'pp.reason',
                    'pp.reward_type',
                    'pu.police_name',
                    'pu.buckle_number',
                    'pu.id AS police_user_id'
                )
                ->where('pp.police_id', $id)
                ->orderBy('pp.created_at', 'desc')
                ->get();

            return view('profile.reward_history', compact('punishments'));
        } catch (\Exception $e) {
            Log::error('Error in policeProfileController@historyOfRewards: ' . $e->getMessage());
            return back()->with('error', 'Unable to load reward history.');
        }
    }
public function salaryIncrementHistory($id)
{
    try {
        $user = Session::get('user');
        if (!$user) {
            return view('profile.salary_increment_history')
                ->with('error', 'Unauthenticated. Please login.')
                ->with('increments', collect([]));
        }

        $query = DB::table('salary_increments AS si')
            ->leftJoin('police_users AS pu', 'si.police_id', '=', 'pu.id')
            ->leftJoin('police_stations AS s', 'si.station_id', '=', 's.id')
            ->leftJoin('districts AS d', 'si.district_id', '=', 'd.id')
            ->leftJoin('states AS st', 'd.state_id', '=', 'st.id')
            ->leftJoin('cities AS c', 'pu.city_id', '=', 'c.id')
            ->select(
                'si.id',
                'si.police_id',
                'si.district_id',
                'si.station_id',
                'si.increment_documents',
                'si.increment_date',
                'si.increment_type',
                'si.new_salary',

                'si.level',
                'si.grade_pay',
                'si.increased_amount',
                'si.created_at',
                'si.updated_at',
                'pu.police_name',
                'pu.buckle_number',
                's.name AS station_name',
                'd.district_name',
                'st.state_name',
                'c.city_name'
            )
            ->where('pu.id', $id); // Only fetch increments for this officer

        // Role-based filtering
        switch ($user['designation_type']) {
            case 'Police':
                if ($user['id'] != $id) {
                    return view('profile.salary_increment_history')
                        ->with('error', 'Unauthorized access.')
                        ->with('increments', collect([]));
                }
                break;

            case 'Station_Head':
                if (!empty($user['police_station_id'])) {
                    $query->where('pu.police_station_id', $user['police_station_id']);
                }
                break;

            case 'Head_Person':
                $query->where('pu.district_id', $user['district_id']);
                break;

            case 'Admin':
                // Admin can access all
                break;

            default:
                return view('profile.salary_increment_history')
                    ->with('error', 'Unauthorized role access.')
                    ->with('increments', collect([]));
        }

        $increments = $query->orderBy('si.created_at', 'desc')->get();

        return view('profile.salary_increment_history', compact('increments'));

    } catch (\Exception $e) {
        Log::error('Error in salaryIncrementHistory: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return view('profile.salary_increment_history')
            ->with('error', 'Unable to load salary increment history.')
            ->with('increments', collect([]));
    }
}


}
