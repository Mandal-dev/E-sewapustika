<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class SalaryIncrementController extends Controller

{
public function index()
{
    try {
        $user = Session::get('user');
        if (!$user) return redirect('/login');

        // Get latest salary increment per police_id
        $latestIncrement = DB::table('salary_increments')
            ->select(
                'id',
                'police_id',
                'increment_type',
                'increment_documents',
                'increment_date',
                'new_salary',
                'level',

                'grade_pay',
                'increased_amount'
            )
            ->whereRaw('id IN (SELECT MAX(id) FROM salary_increments GROUP BY police_id)');

        // Base query for police users joined with master data
        $query = DB::table('police_users AS t4')
            ->leftjoin('districts AS t2', 't4.district_id', '=', 't2.id')
            ->leftjoin('states AS t1', 't2.state_id', '=', 't1.id')
            ->leftjoin('cities AS t3', 't4.city_id', '=', 't3.id')
            ->leftJoinSub($latestIncrement, 't5', function ($join) {
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
                't5.increment_date',
                't5.increment_type',
                't5.increment_documents',
                't5.new_salary',
                't5.level',

                't5.grade_pay',
                't5.increased_amount'
            )
            ->where('t2.is_delete', 'No');

        // Apply role-based filtering
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
                // Admin can see all users — no additional filter needed
                break;
        }

        $polices = $query->orderBy('t4.id', 'desc')->get();

        return view('Salary_Increment.index', compact('polices'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
    }
}

public function search(Request $request)
{
    $search = $request->input('search');
    $designationFilter = $request->input('designation'); // optional
    $user = Session::get('user');
    if (!$user) return response()->json([], 401);

    // Get latest salary increment per police_id
    $latestIncrement = DB::table('salary_increments')
        ->select(
            'id',
            'police_id',
            'increment_type',
            'increment_documents',
            'increment_date',
            'new_salary',
            'level',

            'grade_pay',
            'increased_amount'
        )
        ->whereRaw('id IN (SELECT MAX(id) FROM salary_increments GROUP BY police_id)');

    $query = DB::table('police_users AS t4')
        ->join('districts AS t2', 't4.district_id', '=', 't2.id')
        ->join('states AS t1', 't2.state_id', '=', 't1.id')
        ->join('cities AS t3', 't4.city_id', '=', 't3.id')
        ->leftJoinSub($latestIncrement, 't5', function ($join) {
            $join->on('t4.id', '=', 't5.police_id');
        })
        ->select(
            't1.state_name',
            't2.district_name',
            't3.city_name',
            't4.id AS police_user_id',
            't4.police_name',
            't4.buckle_number',
            't4.designation_type',
            't5.increment_date',
            't5.increment_type',
            't5.increment_documents',
            't5.new_salary',
            't5.level',

            't5.grade_pay',
            't5.increased_amount'
        )
        ->where('t2.is_delete', 'No')
        ->where('t2.status', 'Active');

    // Role-based filter
    switch ($user['designation_type']) {
        case 'Police':
            return response()->json([], 403);
        case 'Station_Head':
            $myStationId = DB::table('police_users')->where('id', $user['id'])->value('police_station_id');
            $query->where('t4.police_station_id', $myStationId);
            break;
        case 'Head_Person':
            $query->where('t4.district_id', $user['district_id']);
            break;
        case 'Admin':
            // No extra filter
            break;
    }

    // Search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('t4.police_name', 'like', "%$search%")
                ->orWhere('t4.buckle_number', 'like', "%$search%")
                ->orWhere('t3.city_name', 'like', "%$search%")
                ->orWhere('t2.district_name', 'like', "%$search%")
                ->orWhere('t1.state_name', 'like', "%$search%");
        });
    }

    // Designation filter
    if ($designationFilter) {
        $query->where('t4.designation_type', $designationFilter);
    }

    $polices = $query->orderBy('t4.id', 'desc')->get();

    return view('Salary_Increment.table_rows', compact('polices'))->render();
}


public function policeSalaryIncrementAdd($id)
{
    $user = Session::get('user');

    // Check if user is logged in
    if (!$user) {
        return response()->json(['error' => 'कृपया लॉगिन करा.'], 403);
    }

    // Only Head_Person can access
    if ($user['designation_type'] !== 'Head_Person') {
        return response()->json(['error' => 'आपल्याला ही क्रिया करण्याची परवानगी नाही.'], 403);
    }

    // Get police user only if in the same district
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
        ->where('t4.district_id', $user['district_id'])
        ->where('t4.id', $id)
        ->first();
    $grade_pay_options = DB::table('stages')->orderBy('id')->get();
    $pay_lavels_options = DB::table('levels')->orderBy('id')->get();
    if (!$police) {
        return response()->json(['error' => 'सदर पोलीस वापरकर्ता अस्तित्वात नाही किंवा आपल्याला परवानगी नाही.'], 404);
    }

    return view('Salary_Increment.add', compact('police', 'grade_pay_options', 'pay_lavels_options'));
}


public function storeSalaryIncrement(Request $request)
{
    $user = Session::get('user');

    Log::info('Salary Increment Request started', [
        'user' => $user,
        'request_data' => $request->all(),
    ]);

    // Only Head_Person can access
    if (!$user || $user['designation_type'] !== 'Head_Person') {
        Log::warning('Unauthorized user attempted to store salary increment', [
            'user' => $user,
        ]);
        return redirect()->back()->with('error', 'आपल्याला वेतनवाढ जोडण्याची परवानगी नाही.');
    }

    // Ensure police_id belongs to the same district
    $policeDistrict = DB::table('police_users')
        ->where('id', $request->police_id)
        ->value('district_id');

    if ($policeDistrict != $user['district_id']) {
        Log::warning('User tried to add increment for another district', [
            'user_district' => $user['district_id'],
            'police_district' => $policeDistrict,
            'police_id' => $request->police_id,
        ]);
        return redirect()->back()->with('error', 'आपल्याला इतर जिल्ह्यातील अधिकारीसाठी वेतनवाढ जोडण्याची परवानगी नाही.');
    }

    // Validation
    $request->validate([
        'police_id'           => 'required|integer',
        'district_id'         => 'required|integer',
        'station_id'          => 'required|integer',
        'increment_date'      => 'required|date',
        'increment_type'      => 'required|string',
        'new_salary'          => 'required|numeric',
        'level_no'               => 'nullable|string|max:10',
        'grade_pay'           => 'nullable|string',
        'increased_amount'    => 'required|numeric',
        'increment_documents' => 'nullable|file|mimes:pdf|max:5120',
    ]);

    try {
        $uniqueFileName = null;

        // Handle file upload
        if ($request->hasFile('increment_documents')) {
            $file = $request->file('increment_documents');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = now()->format('Ymd_His') . '_' . Str::slug($originalName) . '.' . $extension;

            $destinationPath = base_path('uploads/salaryincrements');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $uniqueFileName);

            Log::info('File uploaded successfully', [
                'file_name' => $uniqueFileName,
                'destination_path' => $destinationPath,
            ]);
        }

        $level_value = 'L-' . $request->level_no;
        $grade_value = 'S-' . $request->grade_pay;

        // Insert salary increment
        $insertData = [
            'police_id'           => $request->police_id,
            'district_id'         => $request->district_id,
            'station_id'          => $request->station_id,
            'increment_documents' => $uniqueFileName,
            'increment_date'      => $request->increment_date,
            'increment_type'      => $request->increment_type,
            'new_salary'          => $request->new_salary,
            'level'               => $level_value,
            'grade_pay'           => $grade_value,
            'increased_amount'    => $request->increased_amount,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];

        DB::table('salary_increments')->insert($insertData);

        Log::info('Salary increment stored successfully', [
            'inserted_data' => $insertData,
        ]);

        return redirect()->back()->with('success', 'वेतनवाढ माहिती यशस्वीरित्या जतन झाली.');
    } catch (\Exception $e) {
        Log::error('Error storing salary increment', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);

        return redirect()->back()->with('error', 'वेतनवाढ जतन करताना त्रुटी आली: ' . $e->getMessage());
    }
}


    public function view($filename)
    {
    $path = base_path('uploads/salaryincrements/' . $filename);

    if (!File::exists($path)) {
        abort(404, 'File not found.');
    }

    // Optional: check MIME type dynamically
    $mime = File::mimeType($path);

    return response()->file($path, [
        'Content-Type' => $mime ?? 'application/pdf'
    ]);
    }

    //get salary by lavel and grade pay
public function getSalary(Request $request)
{
    $levelId = $request->get('level_no');   // from dropdown
    $stageId = $request->get('grade_pay');  // from dropdown

    // Log incoming request
    Log::info('Salary API called', [
        'level_no'  => $levelId,
        'grade_pay' => $stageId,
        'all_input' => $request->all()
    ]);

    $salary = DB::table('pay_matrix')
        ->where('level_id', $levelId)
        ->where('stage_id', $stageId)
        ->value('amount');

    // Log query result
    Log::info('Salary fetched from DB', [
        'level_id' => $levelId,
        'stage_id' => $stageId,
        'salary'   => $salary
    ]);

    return response()->json(['salary' => $salary]);
}

}
