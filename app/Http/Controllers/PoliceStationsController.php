<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PoliceStationsController extends Controller
{
    /**
     * Show all police stations (you can update this as needed).
     */
    public function index()
    {
        try {
            $user = Session::get('user');

            if (!$user) {
                return redirect('/login');
            }

            $userId = $user['id'];
            $stations = collect();

            if ($user['designation_type'] === 'Police') {
                return redirect()->back()->with('error', 'Access denied.');
            } elseif ($user['designation_type'] === 'Station_Head') {
                $stations = DB::table('states AS t1')
                    ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                    ->join('police_stations AS t4', 't4.district_id', '=', 't2.id')
                    ->join('police_users AS t3', 't3.police_station_id', '=', 't4.id')
                    ->join('cities AS t5', 't5.id', '=', 't4.city_id')
                    ->where('t3.id', $userId)
                    ->where('t2.is_delete', 'No')
                    ->where('t2.status', 'Active')
                    ->select('t4.name AS station_name', 't1.state_name', 't2.district_name', 't2.status', 't5.city_name')
                    ->orderBy('t2.id', 'desc')
                    ->get();
            } elseif ($user['designation_type'] === 'Head_Person') {
                $stations = DB::table('states AS t1')
                    ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                    ->join('police_stations AS t4', 't4.district_id', '=', 't2.id')
                    ->join('cities AS t5', 't5.id', '=', 't4.city_id')
                    ->select('t4.name AS station_name', 't2.district_name', 't2.status', 't1.state_name', 't5.city_name')
                    ->where('t2.id', $user['district_id'])
                    ->where('t2.is_delete', 'No')
                    ->where('t2.status', 'Active')
                    ->orderBy('t2.id', 'desc')
                    ->get();
            } elseif ($user['designation_type'] === 'Admin') {   // fixed here
                $stations = DB::table('states AS t1')
                    ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                    ->join('police_stations AS t4', 't4.district_id', '=', 't2.id')
                    ->join('cities AS t5', 't5.id', '=', 't4.city_id')
                    ->select('t4.name AS station_name', 't2.district_name', 't2.status', 't1.state_name', 't5.city_name')
                    ->where('t2.is_delete', 'No')
                    ->where('t2.status', 'Active')
                    ->orderBy('t2.id', 'desc')
                    ->get();
            }

            return view('station.index', compact('stations'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }


    public function searchstation(Request $request)
    {
        try {
            $user = Session::get('user');

            if (!$user) {
                return redirect('/login');
            }

            $userId = $user['id'];
            $search = $request->input('search');
            $stations = null;

            if ($user['designation_type'] === 'Police') {
                return redirect()->back()->with('error', 'Access denied.');
            } elseif ($user['designation_type'] === 'Station_Head') {
                $stations = DB::table('states AS t1')
                    ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                    ->join('police_stations AS t4', 't4.district_id', '=', 't2.id')
                    ->join('police_users AS t3', 't3.police_station_id', '=', 't4.id')
                    ->join('cities AS t5', 't5.id', '=', 't4.city_id')
                    ->where('t3.id', $userId)
                    ->where('t2.is_delete', 'No')
                    ->where('t2.status', 'Active')
                    ->select('t4.name AS station_name', 't1.state_name', 't2.district_name', 't2.status', 't5.city_name');
            } elseif ($user['designation_type'] === 'Head_Person') {
                $stations = DB::table('states AS t1')
                    ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                    ->join('police_stations AS t4', 't4.district_id', '=', 't2.id')
                    ->join('cities AS t5', 't5.id', '=', 't4.city_id')
                    ->where('t2.id', $user['district_id'])
                    ->where('t2.is_delete', 'No')
                    ->where('t2.status', 'Active')
                    ->select('t4.name AS station_name', 't2.district_name', 't2.status', 't1.state_name', 't5.city_name');
            } elseif ($user['designation_type'] === 'Admin') {
                $stations = DB::table('states AS t1')
                    ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                    ->join('police_stations AS t4', 't4.district_id', '=', 't2.id')
                    ->join('cities AS t5', 't5.id', '=', 't4.city_id')
                    ->where('t2.is_delete', 'No')
                    ->where('t2.status', 'Active')
                    ->select('t4.name AS station_name', 't2.district_name', 't2.status', 't1.state_name', 't5.city_name');
            } else {
                return redirect()->back()->with('error', 'Role not recognized.');
            }

            // Apply search filter
            if ($search) {
                $stations = $stations->where(function ($query) use ($search) {
                    $query->where('t4.name', 'like', "%{$search}%")
                        ->orWhere('t2.district_name', 'like', "%{$search}%")
                        ->orWhere('t5.city_name', 'like', "%{$search}%")
                        ->orWhere('t1.state_name', 'like', "%{$search}%");
                });
            }

            $stations = $stations->orderBy('t2.id', 'desc')->get();

            return view('station.index', compact('stations'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }


    /**
     * Show create form (modal or page).
     */
    public function create()
    {
        $countries = DB::table('countries')
            ->select('country_name', 'id')
            ->where('is_delete', 'No')
            ->where('status', 'Active')
            ->get();

        $states = DB::table('states')
            ->select('state_name', 'id', 'country_id')
            ->where('is_delete', 'No')
            ->where('status', 'Active')
            ->get();

        $districts = DB::table('districts')
            ->select('district_name', 'id', 'state_id', 'state_id')
            ->where('is_delete', 'No')
            ->where('status', 'Active')
            ->get();

        $cities = DB::table('cities')
            ->where('is_delete', 'No')
            ->where('status', 'Active')
            ->select('id', 'city_name')
            ->orderBy('city_name')
            ->get();

        return view('station.create', compact('countries', 'states', 'districts', 'cities'));
    }

    /**
     * Store new police station.
     */
    public function store(Request $request)
    {
        try {
            // Validate only the fields that exist in the form
            $validated = $request->validate([
                'country_id'  => 'required|integer',
                'state_id'    => 'required|integer',
                'district_id' => 'required|integer',
                'city_id'     => 'required|integer',
                'city_name'   => 'required|string|max:255',
                'status'      => 'required|in:Active,Inactive',
            ]);

            // Insert into database
            DB::table('police_stations')->insert([
                'country_id'          => $validated['country_id'],
                'state_id'            => $validated['state_id'],
                'district_id'         => $validated['district_id'],
                'city_id'             => $validated['city_id'],
                'name'                => $validated['city_name'],
                'status'              => $validated['status'],
                'is_delete'           => 'No',
                'created'             => now(),
                'modified'            => now(),
                'zone_id'             => 0,
                'area_id'             => 0,
                'sdpo_id'             => 0,
                'higher_authority_id' => 0,
                'station_head_id'     => 0,
                'email'               => '',
                'mobile'              => '',
                'address'             => '',
                'latitude'            => '',
                'longitude'           => '',
                'pincode'             => null,
                'image'               => '',
                'police_pc_id'        => 0,
            ]);

            return redirect()->back()->with('success', '✅ पोलीस ठाणे यशस्वीरित्या जतन केले गेले.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ डेटा सेव्ह करताना त्रुटी आली: ' . $e->getMessage())
                ->withInput();
        }
    }



    /**
     * Edit form (optional).
     */
    public function edit($id)
    {
        // Implement if needed
        return view('station.edit', compact('id'));
    }

    /**
     * Update station (optional).
     */
    public function update(Request $request, $id)
    {
        // Implement if needed
    }

    /**
     * Delete station (optional).
     */
    public function destroy($id)
    {
        // Implement soft-delete or hard delete as needed
    }
}
