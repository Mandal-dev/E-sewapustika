<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class punishmentsController extends Controller
{
public function index()
{
    try {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/');
        }

        $perPage = 10;

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
                't4.designation_type AS role',   // 👈 added role
                't5.punishment_given_date',
                't5.reason',
                't5.punishment_type',
                't5.punishment_documents'
            )
            ->where('t2.is_delete', 'No')
            ->where('t2.status', 'Active');

        // designation-based filters
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

        // paginate
        $polices = $query->orderBy('t4.id', 'desc')->paginate($perPage);

        return view('Punishments.index', compact('polices'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
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

        // Save to root-level /uploads/punishments (NOT public)
        $destinationPath = base_path('uploads/punishments');

        // Create directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Move the file
        $file->move($destinationPath, $uniqueFileName);
        Log::info('Punishment PDF stored at: ' . $destinationPath . '/' . $uniqueFileName);

        // Insert into DB
        DB::table('police_punishments')->insert([
            'police_id'             => $request->police_id,
            'district_id'           => $request->district_id,
            'station_id'            => $request->station_id,
            'punishment_given_date' => $request->punishment_given_date,
            'punishment_type'       => $request->punishment_type,
            'reason'                => $request->reason,
            'punishment_documents'  => $uniqueFileName, // only filename stored
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

}
