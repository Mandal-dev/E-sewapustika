<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class PoliceUsersController extends Controller
{
    public function index()
    {
        return view('police_user.index');
    }

    public function indexTable()
    {

        try {
            $user = Session::get('user');
            if (!$user) return redirect('/login');

            $userId = $user['id'];
            $stations     = collect();
            $policeUsers  = collect(); // <-- define it

            if ($user['designation_type'] === 'Police') {
                return redirect()->back()->with('error', 'Access denied.');
            } elseif ($user['designation_type'] === 'Station_Head') {

                // All police under the same station as this station head
                $myStationId = DB::table('police_users')->where('id', $userId)->value('police_station_id');

                $policeUsers = DB::table('police_users AS u')
                    ->join('police_stations AS s', 's.id', '=', 'u.police_station_id')
                    ->where('u.police_station_id', $myStationId)
                    ->where('u.is_delete', 'No')

                    ->select('u.id', 'u.name', 'u.buckle_number', 'u.designation_type', 's.name AS station_name')
                    ->orderBy('u.name')
                    ->get();
            } elseif ($user['designation_type'] === 'Head_Person') {


                // All police in the same district
                $policeUsers = DB::table('police_users AS u')
                    ->join('police_stations AS s', 's.id', '=', 'u.police_station_id')
                    ->where('u.district_id', $user['district_id'])
                    ->where('u.is_delete', 'No')

                    ->select('u.id', 'u.police_name', 'u.buckle_number', 'u.designation_type', 's.name AS station_name')
                    ->orderBy('u.police_name')
                    ->get();
            } elseif ($user['designation_type'] === 'Admin') {



                // All police (system-wide)
                $policeUsers = DB::table('police_users AS u')
                    ->join('police_stations AS s', 's.id', '=', 'u.police_station_id')
                    ->where('u.is_delete', 'No')

                    ->select('u.id', 'u.police_name', 'u.buckle_number', 'u.designation_type', 's.name AS station_name')
                    ->orderBy('u.police_name')
                    ->get();
            }

            return view('police_user.table', compact('policeUsers')); // <-- pass it
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }



    public function create()
    {
        $countries = DB::table('countries')
            ->select('id', 'country_name')
            ->where('is_delete', 'No')
            ->get();

        $designations = DB::table('mst_police_designations')
            ->select('id', 'designation_name')
            ->get();

        $religions = DB::table('mst_religions')
            ->select('id', 'name')
            ->get();

        return view('police_user.create', compact('countries', 'designations', 'religions'));
    }



    public function store(Request $request)
    {
        Log::info('Police store request received', $request->all());

        $validator = Validator::make($request->all(), [
            'police_name'      => 'required|string|max:150',
            'gender'           => 'required|in:Male,Female,Other',
            'mobile'           => 'nullable|string|max:10',
            'email'            => 'nullable|email|max:150',
            'buckle_number'    => 'nullable|string|max:50',
            'district_id'      => 'nullable|integer|exists:districts,id',
            'state_id'         => 'nullable|integer|exists:states,id',
            'city_id'          => 'nullable|integer|exists:cities,id',
            'station_id'       => 'nullable|integer|exists:police_stations,id',
            'designation_id'   => 'nullable|integer|exists:mst_police_designations,id',
            'designation_type' => 'nullable|string|max:100',
            'caste'            => 'nullable|string|max:100',
            'sub_caste'        => 'nullable|string|max:100',
            'joining_date'     => 'nullable|date',
            'retirement_date'  => 'nullable|date|after_or_equal:joining_date',
            'address'          => 'nullable|string',
            'religion'         => 'nullable|string|max:100',
            'pincode'          => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            Log::warning('Police store validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = [
                'country_id'        => 1,
                'police_name'      => $request->police_name,
                'gender'           => $request->gender,
                'mobile'           => $request->mobile,
                'email'            => $request->email,
                'buckle_number'    => $request->buckle_number,
                'district_id'      => $request->district_id,
                'state_id'         => $request->state_id,
                'city_id'          => $request->city_id,
                'police_station_id'       => $request->station_id,
                'designation_id'   => $request->designation_id,
                'designation_type' => $request->designation_type,
                'category'        => $request->sub_caste,
                'caste'            => $request->caste,

                'joining_date'     => $request->joining_date,
                'retirement_date'  => $request->retirement_date,
                'address'          => $request->address,
                'religion'         => $request->religion,
                'pincode'          => $request->pincode,
                'created_at'       => now(),
                'updated_at'       => now(),
                'deleted_at'       => null,
            ];

            Log::info('Police store validated data ready to insert', $data);

            DB::table('police_users')->insert($data);

            Log::info('Police store success: record inserted', ['police_name' => $request->police_name]);

            return redirect()->back()->with('success', 'Registration successful!');
        } catch (Exception $e) {
            Log::error('Police store exception', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    public function search(Request $request)
    {
        try {
            $user = Session::get('user');
            if (!$user) return redirect('/login');

            $keyword = $request->input('search', null); // Search input
            $userId = $user['id'];

            $query = DB::table('police_users AS u')
                ->join('police_stations AS s', 's.id', '=', 'u.police_station_id')
                ->where('u.is_delete', 'No');


            // Role-wise filtering
            switch ($user['designation_type']) {
                case 'Police':
                    return response()->json(['error' => 'Access denied.'], 403);

                case 'Station_Head':
                    $myStationId = DB::table('police_users')
                        ->where('id', $userId)
                        ->value('police_station_id');
                    $query->where('u.police_station_id', $myStationId);
                    break;

                case 'Head_Person':
                    if (!isset($user['district_id'])) {
                        return response()->json(['error' => 'District ID missing.'], 400);
                    }
                    $query->where('u.district_id', $user['district_id']);
                    break;

                case 'Admin':
                    // No restriction
                    break;

                default:
                    return response()->json(['error' => 'Role not recognized.'], 400);
            }

            // Keyword search
            if ($keyword) {
                $keyword = strtolower($keyword); // make search term lowercase
                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(u.police_name) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(u.buckle_number) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(u.designation_type) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(s.name) LIKE ?', ["%{$keyword}%"]);
                });
            }


            $policeUsers = $query->select(
                'u.id',
                'u.police_name',
                'u.buckle_number',
                'u.designation_type',
                's.name AS station_name'
            )
                ->orderBy('u.police_name')
                ->get();

            // Return partial table view for AJAX
            return view('police_user.table', compact('policeUsers'))->render();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Title');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Gender');
        $sheet->setCellValue('D1', 'Designation');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'Mobile');
        $sheet->setCellValue('G1', 'Country');
        $sheet->setCellValue('H1', 'State');
        $sheet->setCellValue('I1', 'District');
        $sheet->setCellValue('J1', 'Police Station');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'clerical_employee_template.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
    public function import(Request $request)
    {
        // Get logged-in user
        $user = Session::get('user');
        if (!$user) {
            return redirect('/login');
        }

        // Role check
        if ($user['designation_type'] === 'Police') {
            return back()->with('error', 'Access denied. You are not allowed to upload CSV files.');
        }

        // Validate CSV

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:102400', // max in KB → 100 MB = 100*1024 KB
        ]);


        $filePath = $request->file('file')->getRealPath();
        $successCount = 0;
        $batchSize = 500; // insert in batches
        $batchData = [];

        // Log file for errors
        $logFile = storage_path('logs/csv_import_errors.log');
        file_put_contents($logFile, ""); // clear old log

        if (($handle = fopen($filePath, 'r')) !== false) {
            $rowIndex = 0;

            while (($data = fgetcsv($handle, 0, ",")) !== false) {
                $rowIndex++;

                // Skip header
                if ($rowIndex == 1) continue;

                $title        = trim($data[0] ?? '');
                $name         = trim($data[1] ?? '');
                $gender       = ucfirst(strtolower(trim($data[2] ?? '')));
                $designation  = trim($data[3] ?? '');
                $email        = trim($data[4] ?? '');
                $mobile       = trim($data[5] ?? '');
                $districtName = trim($data[8] ?? '');
                $stationName  = trim($data[9] ?? '');
                $buckleNumber = trim($data[11] ?? null);

                // Skip empty row
                if (!$name && !$gender && !$designation && !$email && !$mobile && !$districtName && !$stationName) {
                    continue;
                }

                // Required fields check
                $missingFields = [];
                if (!$name) $missingFields[] = 'Name';
                if (!$gender) $missingFields[] = 'Gender';
                if (!$designation) $missingFields[] = 'Designation';
                if (!$email) $missingFields[] = 'Email';
                if (!$mobile) $missingFields[] = 'Mobile';
                if (!$districtName) $missingFields[] = 'District';
                if (!$stationName) $missingFields[] = 'Police Station';

                if (!empty($missingFields)) {
                    file_put_contents($logFile, "Row $rowIndex: Missing fields - " . implode(', ', $missingFields) . "\n", FILE_APPEND);
                    continue;
                }

                // Normalize gender
                if ($gender == 'M') $gender = 'Male';
                elseif ($gender == 'F') $gender = 'Female';
                elseif (!in_array($gender, ['Male', 'Female', 'Other'])) $gender = 'Other';

                // District and station check
                $district = DB::table('districts')->where('district_name', $districtName)->first();
                $station  = $district ? DB::table('police_stations')->where('district_id', $district->id)->where('name', $stationName)->first() : null;

                if (!$district || !$station) {
                    file_put_contents($logFile, "Row $rowIndex: Invalid district or station\n", FILE_APPEND);
                    continue;
                }

                // Email/mobile uniqueness
                if (DB::table('police_users')->where('email', $email)->exists()) {
                    file_put_contents($logFile, "Row $rowIndex: Email '$email' already exists\n", FILE_APPEND);
                    continue;
                }

                if (DB::table('police_users')->where('mobile', $mobile)->exists()) {
                    file_put_contents($logFile, "Row $rowIndex: Mobile '$mobile' already exists\n", FILE_APPEND);
                    continue;
                }

                // Optional: map designation to designation_id
                $designationRow = DB::table('designations')->where('name', $designation)->first();
                $designationId = $designationRow->id ?? null;

                // Prepare batch
                $batchData[] = [
                    'country_id'        => 1,
                    'state_id'          => $district->state_id ?? null,
                    'district_id'       => $district->id,
                    'police_station_id' => $station->id,
                    'police_name'       => $name,
                    'email'             => $email,
                    'mobile'            => $mobile,
                    'designation_id'    => $designationId,
                    'designation_type'  => 'Police',
                    'post'              => $designation,
                    'gender'            => $gender,
                    'buckle_number'     => $buckleNumber,
                    'is_active'         => 1,
                    'is_delete'         => 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];

                // Insert in batch
                if (count($batchData) >= $batchSize) {
                    try {
                        DB::table('police_users')->insert($batchData);
                        $successCount += count($batchData);
                        $batchData = [];
                    } catch (\Exception $e) {
                        foreach ($batchData as $bIndex => $row) {
                            file_put_contents($logFile, "Row $rowIndex (batch): " . $e->getMessage() . "\n", FILE_APPEND);
                        }
                        $batchData = [];
                    }
                }
            }

            // Insert remaining rows
            if (!empty($batchData)) {
                try {
                    DB::table('police_users')->insert($batchData);
                    $successCount += count($batchData);
                } catch (\Exception $e) {
                    foreach ($batchData as $row) {
                        file_put_contents($logFile, "Row $rowIndex (batch): " . $e->getMessage() . "\n", FILE_APPEND);
                    }
                }
            }

            fclose($handle);
        } else {
            return back()->with('error', 'Could not open the CSV file.');
        }

        $message = "$successCount users imported successfully. Check log file for errors: storage/logs/csv_import_errors.log";
        return back()->with('success', $message);
    }

    public function edit($id)
    {
        $police = DB::table('police_users AS u')
            ->leftJoin('states AS st', 'st.id', '=', 'u.state_id')
            ->leftJoin('districts AS d', 'd.id', '=', 'u.district_id')
            ->leftJoin('cities AS c', 'c.id', '=', 'u.city_id')
            ->leftJoin('police_stations AS ps', 'ps.id', '=', 'u.police_station_id')
            ->leftJoin('mst_police_designations AS des', 'des.id', '=', 'u.designation_id')
            ->leftJoin('mst_religions AS religi', 'religi.id', '=', 'u.religion')

            ->where('u.id', $id)
            ->select('u.*', 'st.state_name', 'religi.name', 'd.district_name', 'c.city_name', 'ps.name AS station_name', 'des.designation_name')
            ->first();

        if (!$police) {
            return redirect()->back()->with('error', 'Police record not found.');
        }

        $countries = DB::table('countries')
            ->where('status', 'Active')
            ->where('id', $police->country_id)
            ->get();
        $states       = DB::table('states')->where('status', 'Active')->where('id', $police->state_id)->get();
        $districts    = DB::table('districts')->where('status', 'Active')->where('id', $police->district_id)->get();
        $cities       = DB::table('cities')->where('status', 'Active')->where('district_id', $police->district_id)->get();
        $stations     = DB::table('police_stations')->where('status', 'Active')->where('district_id', $police->district_id)->get();
        $designations = DB::table('mst_police_designations')->where('status', 'Active')->get();
        $religions    = DB::table('mst_religions')->get();

        return view('police_user.edit', compact('police', 'countries', 'states', 'districts', 'cities', 'stations', 'designations', 'religions'));
    }


    public function update(Request $request, $id)
    {
        Log::info('Police update request received', $request->all());

        $validator = Validator::make($request->all(), [
            'police_name'      => 'required|string|max:150',
            'gender'           => 'required|in:Male,Female,Other',
            'mobile'           => 'nullable|string|max:10',
            'email'            => 'nullable|email|max:150',
            'buckle_number'    => 'nullable|string|max:50',
            'district_id'      => 'nullable|integer|exists:districts,id',
            'state_id'         => 'nullable|integer|exists:states,id',
            'city_id'          => 'nullable|integer|exists:cities,id',
            'station_id'       => 'nullable|integer|exists:police_stations,id',
            'designation_id'   => 'nullable|integer|exists:mst_police_designations,id',
            'designation_type' => 'nullable|string|max:100',
            'caste'            => 'nullable|string|max:100',
            'category'         => 'nullable|string|max:100',
            'joining_date'     => 'nullable|date',
            'retirement_date'  => 'nullable|date|after_or_equal:joining_date',
            'address'          => 'nullable|string',
            'religion'         => 'nullable|string|max:100',
            'pincode'          => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            Log::warning('Police update validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = [
                'police_name'       => $request->police_name,
                'gender'            => $request->gender,
                'mobile'            => $request->mobile,
                'email'             => $request->email,
                'buckle_number'     => $request->buckle_number,
                'district_id'       => $request->district_id,
                'state_id'          => $request->state_id,
                'city_id'           => $request->city_id,
                'police_station_id' => $request->station_id,
                'designation_id'    => $request->designation_id,
                'designation_type'  => $request->designation_type,
                'caste'             => $request->caste,
                'category'          => $request->category,
                'joining_date'      => $request->joining_date,
                'retirement_date'   => $request->retirement_date,
                'address'           => $request->address,
                'religion'          => $request->religion,
                'pincode'           => $request->pincode,
                'updated_at'        => now(),
            ];

            DB::table('police_users')->where('id', $id)->update($data);

            Log::info('Police update success', ['id' => $id]);

            return redirect()->route('police.index')->with('success', 'Police record updated successfully!');
        } catch (Exception $e) {
            Log::error('Police update exception', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }
}
