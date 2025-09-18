<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;



class DistrictsController extends Controller
{
    public function index()
    {
        $user = Session::get('user');

        if (!$user) {
            return redirect()->route('login.page')->with('error', 'Unauthenticated');
        }

        $userId = $user['id'];
        $designation = $user['designation_type'];

        if ($designation === 'Police') {
            // Police not allowed
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Admin can see all districts
        if ($designation === 'Admin') {
            $districts = DB::table('states AS t1')
                ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                ->select('t1.state_name', 't2.district_name', 't2.status', 't2.id')
                ->where('t2.is_delete', 'No')
                ->where('t2.status', 'Active')
                ->orderBy('t2.id', 'desc')
                ->get();
        } elseif ($designation === 'Station_Head') {
            $districts = DB::table('states AS t1')
                ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                ->join('police_users AS t3', 't3.district_id', '=', 't2.id')
                ->select('t1.state_name', 't2.district_name', 't2.status', 't2.id')
                ->where('t2.is_delete', 'No')
                ->where('t2.status', 'Active')
                ->where('t3.id', $userId)
                ->where('t3.district_id', $user['district_id'])
                ->where('t2.id', $user['district_id'])
                ->orderBy('t2.id', 'desc')
                ->get();
        } elseif ($designation === 'Head_Person') {
            $districts = DB::table('states AS t1')
                ->join('districts AS t2', 't1.id', '=', 't2.state_id')
                ->join('police_users AS t3', 't3.district_id', '=', 't2.id')
                ->select('t1.state_name', 't2.district_name', 't2.status', 't2.id')
                ->where('t2.is_delete', 'No')
                ->where('t2.status', 'Active')
                ->where('t3.id', $userId)
                ->where('t3.district_id', $user['district_id'])
                ->where('t2.id', $user['district_id'])
                ->orderBy('t2.id', 'desc')
                ->get();
        } else {
            // Default: no access for unknown roles
            return redirect()->back()->with('error', 'Invalid user role.');
        }

        return view('districts.index', compact('districts'));
    }


    public function create()
    {
        $countries = DB::table('countries')
            ->select('country_name', 'id')       // only fetch required fields
            ->where('is_delete', 'No')           // filtering soft-deleted entries
            ->where('status', 'Active')          // only active countries
            ->get();

        $states = DB::table('states')
            ->select('state_name', 'id')
            ->where('is_delete', 'No')
            ->where('status', 'Active')
            ->get();

        return view('districts.create', compact('countries', 'states'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'district_name' => 'required|string|max:255',
        ]);

        DB::table('districts')->insert([
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'district_name' => $request->district_name,
            'district_name_marathi' => $request->district_name_marathi,
            'district_name_hindi' => $request->district_name_hindi,
            'status' => $request->status ?? 'Active',
            'is_delete' => 'No',
            'created' => Carbon::now(),
            'modified' => now(),
        ]);

        return redirect()->route('districts.index')->with('success', 'District created successfully.');
    }

    public function edit($id)
    {
        $district = DB::table('districts')->where('id', $id)->first();
        return view('districts.edit', compact('district'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'district_name' => 'required|string|max:255',
        ]);

        DB::table('districts')->where('id', $id)->update([
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'district_name' => $request->district_name,
            'district_name_marathi' => $request->district_name_marathi,
            'district_name_hindi' => $request->district_name_hindi,
            'status' => $request->status ?? 'Active',
            'modified' => Carbon::now(),
        ]);

        return redirect()->route('districts.index')->with('success', 'District updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('districts')->where('id', $id)->update(['is_delete' => 1]);
        return redirect()->route('districts.index')->with('success', 'District deleted (soft) successfully.');
    }
}
