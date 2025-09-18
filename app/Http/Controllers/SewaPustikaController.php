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
                    't6.name AS police_station_name',
                    't5.sewapusticapath',
                    't4.post',
                    't4.mobile'
                )
                ->where('t4.is_delete', 'No');

            // designation-based filters
            switch ($user['designation_type']) {
                case 'Police':
                    $query->where('t4.id', $user['id']);
                    break;

                case 'Station_Head':
                    $myStationId = DB::table('police_users')->where('id', $user['id'])->value('police_station_id');
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

            // paginate
            $polices = $query->orderBy('t4.id', 'desc')->paginate($perPage);

            return view('sewa_pustika.index', compact('polices'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
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



    public function store(Request $request)
    {
        Log::info('Sewa Pustika store method hit', ['request' => $request->all()]);

        $user = Session::get('user');

        // ✅ Check if user is logged in
        if (!$user) {
            Log::warning('Unauthorized attempt: user not logged in');
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
            Log::warning('Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors());
        }

        // Get police record
        $police = DB::table('police_users')->where('id', $request->police_id)->first();
        if (!$police) {
            Log::warning('Police not found', ['police_id' => $request->police_id]);
            return redirect()->back()->with('error', 'सदर पोलीस वापरकर्ता आढळला नाही.');
        }

        // ✅ Role-based access
        $authorized = false;
        switch ($user['designation_type']) {
            case 'Police':
                if ($police->id == $user['id']) $authorized = true;
                break;
            case 'Station_Head':
                if ($police->police_station_id == $user['police_station_id']) $authorized = true;
                break;
            case 'Head_Person':
                if ($police->district_id == $user['district_id']) $authorized = true;
                break;
            case 'Admin':
                $authorized = true;
                break;
        }

        if (!$authorized) {
            Log::warning('Unauthorized upload attempt', [
                'user_id' => $user['id'],
                'user_type' => $user['designation_type'],
                'police_id' => $request->police_id,
            ]);
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
                Log::info('Uploads directory created', ['path' => $destinationPath]);
            }

            $file->move($destinationPath, $uniqueFileName);
            Log::info('PDF stored', ['path' => $destinationPath . '/' . $uniqueFileName]);

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
                Log::info('Sewa Pustika record inserted successfully', ['police_id' => $request->police_id]);
                return redirect()->back()->with('success', 'सेवा पुस्तिका यशस्वीरित्या अपलोड केली गेली.');
            } else {
                Log::error('DB insert failed', ['police_id' => $request->police_id]);
                return redirect()->back()->with('error', 'सेवा पुस्तिका जतन करताना त्रुटी आली.');
            }
        } catch (\Exception $e) {
            Log::error('Error storing Sewa Pustika', [
                'exception' => $e->getMessage(),
                'police_id' => $request->police_id,
            ]);
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
            // Get logged-in user
            $user = Session::get('user');
            if (!$user) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return redirect('/login');
            }

            $perPage = 10;
            $keyword = $request->input('keyword');

            // Latest sewa_pustika per police user
            $latestPustika = DB::table('sewa_pustikas as sp')
                ->select('sp.id', 'sp.police_id', 'sp.sewa_pustika_status', 'sp.sewapusticapath')
                ->whereIn('sp.id', function ($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('sewa_pustikas')
                        ->groupBy('police_id');
                });

            // Base query
            $query = DB::table('police_users as t4')
                ->leftJoin('districts as t2', 't4.district_id', '=', 't2.id')
                ->leftJoin('states as t1', 't2.state_id', '=', 't1.id')
                ->leftJoin('cities as t3', 't4.city_id', '=', 't3.id')
                ->leftJoin('police_stations as t6', 't4.police_station_id', '=', 't6.id')
                ->leftJoinSub($latestPustika, 't5', function ($join) {
                    $join->on('t4.id', '=', 't5.police_id');
                })
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
                    't5.sewapusticapath',
                    't4.post',
                    't4.mobile'
                )
                ->where('t4.is_delete', 'No');

            // Role-based filters
            switch ($user['designation_type']) {
                case 'Police':
                    if ($request->ajax()) {
                        return response()->json(['error' => 'Access denied.'], 403);
                    }
                    return redirect()->back()->with('error', 'Access denied.');
                case 'Station_Head':
                    $myStationId = DB::table('police_users')->where('id', $user['id'])->value('police_station_id');
                    $query->where('t4.police_station_id', $myStationId);
                    break;
                case 'Head_Person':
                    $query->where('t4.district_id', $user['district_id']);
                    break;
                case 'Admin':
                    // no extra filter
                    break;
                default:
                    if ($request->ajax()) {
                        return response()->json(['error' => 'Unauthorized access.'], 403);
                    }
                    return redirect()->back()->with('error', 'Unauthorized access.');
            }

            // Search filter (case-insensitive)
            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('t4.police_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('t4.buckle_number', 'LIKE', "%{$keyword}%")
                        ->orWhere('t4.designation_type', 'LIKE', "%{$keyword}%")
                        ->orWhere('t3.city_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('t2.district_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('t1.state_name', 'LIKE', "%{$keyword}%");
                });
            }

            // Pagination
            $polices = $query->orderBy('t4.id', 'desc')
                ->paginate($perPage)
                ->appends(['keyword' => $keyword]);

            // AJAX response
            if ($request->ajax()) {
                return view('sewa_pustika.search_table', compact('polices'))->render();
            }

            // Normal page load
            return view('sewa_pustika.search_table', compact('polices'));
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Sewa Pustika Search Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
            }
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
