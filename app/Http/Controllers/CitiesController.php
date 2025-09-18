<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

class CitiesController extends Controller
{
public function index()
{
    $user = Session::get('user');

    if (!$user) {
        return redirect()->route('login.page')->with('error', 'Unauthenticated');
    }

    $userId      = $user['id'];
    $designation = $user['designation_type'];
    $role        = $user['role'] ?? null; // <-- added role support

    // 🚫 Restrict Police
    if ($designation === 'Police' || $role === 'Police') {
        return redirect()->back()->with('error', 'Access denied.');
    }

    // ✅ Admin & Super_Admin: all districts
    if ($designation === 'Admin' || $role === 'Admin' || $role === 'Super_Admin') {
        $cities = DB::table('states AS t1')
            ->join('districts AS t2', 't1.id', '=', 't2.state_id')
            ->join('police_users AS t3', 't3.district_id', '=', 't2.id')
            ->join('cities AS t5', 't3.city_id', '=', 't5.id')
            ->select('t1.state_name', 't2.district_name', 't2.status', 't2.id', 't5.city_name')
            ->where('t2.is_delete', 'No')
            ->where('t2.status', 'Active')
            ->orderBy('t2.id', 'desc')
            ->get();
    }
    // ✅ Station Head: only their district
    elseif ($designation === 'Station_Head' || $role === 'Station_Head') {
        $cities = DB::table('states AS t1')
            ->join('districts AS t2', 't1.id', '=', 't2.state_id')
            ->join('police_users AS t3', 't3.district_id', '=', 't2.id')
            ->join('cities AS t5', 't3.city_id', '=', 't5.id')
            ->select('t1.state_name', 't2.district_name', 't2.status', 't2.id', 't5.city_name')
            ->where('t2.is_delete', 'No')
            ->where('t2.status', 'Active')
            ->where('t3.id', $userId)
            ->where('t3.district_id', $user['district_id'])
            ->where('t2.id', $user['district_id'])
            ->orderBy('t2.id', 'desc')
            ->get();
    }
    // ✅ Head Person: only their district
    elseif ($designation === 'Head_Person' || $role === 'Head_Person') {
        $cities = DB::table('states AS t1')
            ->join('districts AS t2', 't1.id', '=', 't2.state_id')
            ->join('police_users AS t3', 't3.district_id', '=', 't2.id')
            ->join('cities AS t5', 't3.city_id', '=', 't5.id')
            ->select('t1.state_name', 't2.district_name', 't2.status', 't2.id', 't5.city_name')
            ->where('t2.is_delete', 'No')
            ->where('t2.status', 'Active')
            ->where('t3.id', $userId)
            ->where('t3.district_id', $user['district_id'])
            ->where('t2.id', $user['district_id'])
            ->orderBy('t2.id', 'desc')
            ->get();
    }
    else {
        return redirect()->back()->with('error', 'Invalid user role.');
    }

    return view('city.index', compact('cities'));
}


public function create()
{
    $countries = DB::table('countries')
        ->select('country_name', 'id')
        ->where('is_delete', 'No')
        ->where('status', 'Active')
        ->get();

    $states = DB::table('states')
        ->select('state_name', 'id')
        ->where('is_delete', 'No')
        ->where('status', 'Active')
        ->get();

    $districts = DB::table('districts')
        ->select('district_name', 'id')
        ->where('is_delete', 'No')
        ->where('status', 'Active')
        ->get();



    return view('city.create', compact('countries', 'states', 'districts'));
}


public function store(Request $request)
{
    // ✅ Step 1: Validate input
    $request->validate([
        'country_id' => 'required|integer',
        'state_id' => 'required|integer',
        'district_id' => 'required|integer',
        'city_name' => 'required|string|max:255',
        'city_name_marathi' => 'nullable|string|max:255',
        'city_name_hindi' => 'nullable|string|max:255',
        'status' => 'required|in:Active,Inactive',
    ]);

    try {
        // ✅ Step 2: Insert into database
        DB::table('cities')->insert([
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'city_name' => $request->city_name,
            'city_name_marathi' => $request->city_name_marathi,
            'city_name_hindi' => $request->city_name_hindi,
            'status' => $request->status,
            'is_delete' => 'No',
            'created' => Carbon::now(),
            'modified' => Carbon::now(),
        ]);

        // ✅ Step 3: Redirect to city index or back with success
        return redirect()->route('city.index')->with('success', 'शहर यशस्वीरित्या जतन केले गेले.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'शहर जतन करताना त्रुटी आली: ' . $e->getMessage());
    }
}
    public function edit($id)
    {
        // Logic to show the form for editing a city
        return view('city.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update the city data
    }

    public function destroy($id)
    {
        // Logic to delete a city
    }

public function getCitiesByDistrict($districtId)
{
    $cities = DB::table('cities')
        ->where('district_id', $districtId)
        ->where('is_delete', 'No')
        ->where('status', 'Active')
        ->select('id', 'city_name')
        ->orderBy('city_name')
        ->get();

    return response()->json($cities);
}

}

