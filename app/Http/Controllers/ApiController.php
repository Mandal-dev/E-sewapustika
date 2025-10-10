<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ApiController extends Controller
{


    public function loginNow(Request $request)
    {
        try {
            $inputData = $request->all();
            foreach ($inputData as $key => $value) {
                $$key = $value;
            }

            // ✅ Validate input
            if (empty($user_name) || empty($user_pass)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Username and password are required.'
                ], 400); // 400 = Bad Request
            }

            // ✅ Fetch user with joins
            $user = DB::table('tbl_login AS t1')
                ->join('tbl_shop AS t2', 't1.user_ref_id', '=', 't2.shop_id')
                ->join('tbl_zone AS t3', 't2.shop_zone_id', '=', 't3.zone_id')
                ->join('tbl_district AS t4', 't3.zone_dist_id', '=', 't4.dist_id')
                ->join('tbl_state AS t5', 't4.dist_state_id', '=', 't5.state_id')
                ->where('t1.user_name', $user_name)
                ->where('t1.user_pass', md5($user_pass))
                ->where('t1.user_type', 'Shop')
                ->select([
                    't1.user_type',
                    't1.user_id',
                    't1.user_email',
                    't2.shop_id',
                    't2.shop_name',
                    't2.shop_no',
                    't2.shop_owner',
                    't2.shop_add',
                    't3.zone_name',
                    't4.dist_name',
                    't5.state_name'
                ])
                ->first();

            // ❌ Invalid login
            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Incorrect username or password.'
                ], 401); // 401 = Unauthorized
            }

            // 🔹 Remove any existing tokens
            DB::table('personal_access_tokens')
                ->where('tokenable_type', 'App\\Models\\TblLogin')
                ->where('tokenable_id', $user->user_id)
                ->delete();

            // 🔹 Generate new token
            $plainToken = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $plainToken);

            DB::table('personal_access_tokens')->insert([
                'tokenable_type' => 'App\\Models\\TblLogin',
                'tokenable_id'   => $user->user_id,
                'name'           => 'mobile-app',
                'token'          => $hashedToken,
                'created_at'     => now(),
            ]);

            // ✅ Return success response
            return response()->json([
                'status' => 1,
                'message' => 'Login successful.',
                'token' => $plainToken,
                'data' => $user
            ], 200); // 200 = OK

        } catch (\Exception $e) {
            // ⚠️ Handle any unexpected errors
            return response()->json([
                'status' => 0,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500); // 500 = Internal Server Error
        }
    }

    public function getPhaseData(Request $request)
    {
        try {
            // ✅ Get user_id from token
            $user_id = $request->user_id;

            // ✅ Get shop_id from request
            $shop_id = $request->input('shop_id');

            // 🔹 Verify both IDs are provided
            if (empty($user_id) || empty($shop_id)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Missing required parameters: user_id or shop_id.'
                ], 400);
            }

            // 🔹 Verify that the shop belongs to the logged-in user
            $loginRecord = DB::table('tbl_login')
                ->where('user_id', $user_id)
                ->where('user_ref_id', $shop_id)
                ->first();

            if (!$loginRecord) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthorized: This shop does not belong to the logged-in user.'
                ], 403);
            }

            // ✅ Fetch phase data
            $result = DB::table('tbl_phase_two AS t1')
                ->join('tbl_phase_one AS t2', 't1.pt_po_id', '=', 't2.po_id')
                ->where('t1.pt_shop_id', $shop_id)
                ->where('t2.po_status', '2')
                ->select([
                    't1.pt_id',
                    't1.pt_uniq_id',
                    't1.pt_shop_id',
                    't1.pt_year',
                    't1.pt_month',
                    't1.pt_taken_q',
                    't1.pt_taken_k',
                    't1.pt_taken_g',
                    't1.pt_date',
                    't2.po_id',
                    't2.po_uniq'
                ])
                ->groupBy(
                    't1.pt_id',
                    't1.pt_uniq_id',
                    't1.pt_shop_id',
                    't1.pt_year',
                    't1.pt_month',
                    't1.pt_taken_q',
                    't1.pt_taken_k',
                    't1.pt_taken_g',
                    't1.pt_date',
                    't2.po_id',
                    't2.po_uniq'
                )
                ->get();

            // ✅ Response
            if ($result->isEmpty()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Data not found.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 1,
                'message' => 'Successful.',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //=========================







    public function getPhaseDataTwo(Request $request)
    {
        try {
            // ✅ Get logged-in user ID from middleware
            $user_id = $request->user_id;

            // ✅ Get shop_id and pt_id from request
            $request->validate([
                'shop_id' => 'required|integer',
                'pt_id'   => 'required|integer',
            ]);

            // ✅ If validation passes, safely retrieve the inputs
            $shop_id = $request->input('shop_id');
            $pt_id   = $request->input('pt_id');

            // 🔹 Validate required fields
            if (!$shop_id || !$pt_id) {
                return response()->json([
                    'status' => 0,
                    'message' => 'shop_id and pt_id are required.'
                ], 400);
            }

            // 🔹 Verify that the shop belongs to logged-in user
            $loginRecord = DB::table('tbl_login')
                ->where('user_id', $user_id)
                ->where('user_ref_id', $shop_id)
                ->first();

            if (!$loginRecord) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthorized: This shop does not belong to the logged-in user.'
                ], 403);
            }



            // ✅ Query Builder to get phase two data
            $result = DB::table('tbl_phase_two AS t1')
                ->join('tbl_phase_one AS t2', 't1.pt_po_id', '=', 't2.po_id')
                ->join('tbl_zone AS t3', 't2.po_zone_id', '=', 't3.zone_id')
                ->join('tbl_godown AS t4', 't2.po_god_id', '=', 't4.god_id')
                ->join('tbl_scheme AS t5', 't2.po_scheme_id', '=', 't5.scheme_id')

                ->join('tbl_cereal AS t6', 't2.po_cereal_id', '=', 't6.cereal_id')
                ->join('tbl_driver AS t7', 't2.po_driver_id', '=', 't7.driver_id')
                ->join('tbl_contractor AS t8', 't2.po_cont_id', '=', 't8.cont_id')
                ->join('tbl_shop AS t9', 't1.pt_shop_id', '=', 't9.shop_id')
                ->where('t1.pt_shop_id', $shop_id)
                ->where('t1.pt_po_id', $pt_id)
                ->select([
                    't1.pt_status',
                    't1.pt_id',
                    't1.pt_uniq_id',
                    't1.pt_shop_id',
                    't1.pt_year',
                    't1.pt_month',
                    't1.pt_taken_q',
                    't1.pt_taken_k',
                    't1.pt_taken_g',
                    't1.pt_date',
                    't2.po_id',
                    't2.po_uniq',
                    't3.zone_name',
                    't4.god_no',
                    't4.god_name',
                    't5.scheme_name',
                    't6.cereal_name',
                    't7.driver_name',
                    't7.driver_mno',
                    't7.driver_truck_no',
                    't8.cont_name',
                    't9.shop_name',
                    't9.shop_no',
                    't9.shop_mno'
                ])
                ->first();

            // 🔹 Prepare response
            if (!$result) {
                return response()->json([
                    'status' => 1,
                    'message' => 'No data found.',
                    'data' => (object)[]
                ], 404);
            }

            // if ($result->pt_status == '1') {
            //     return response()->json([
            //         'status' => 0,
            //         'message' => 'Data submitted..!!'
            //     ]);
            // }

            return response()->json([
                'status' => 1,
                'message' => 'Successful..',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //====================







    public function phaseThreeFinalSubmit(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $shop_id = $request->input('shop_id');

            // 🔹 Verify shop ownership
            $loginRecord = DB::table('tbl_login')
                ->where('user_id', $user_id)
                ->where('user_ref_id', $shop_id)
                ->first();

            if (!$loginRecord) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthorized: This shop does not belong to the logged-in user.'
                ], 403);
            }

            // ✅ Inputs
            $pt_id = $request->input('pt_id');
            $po_id = $request->input('po_id');
            $ptt_taken_q = $request->input('ptt_taken_q');
            $ptt_taken_k = $request->input('ptt_taken_k');
            $ptt_taken_g = $request->input('ptt_taken_g');
            $ptt_user_id = $request->input('ptt_user_id');
            $ptt_lat = $request->input('ptt_lat');
            $ptt_long = $request->input('ptt_long');
            $shop_lat = $request->input('shop_lat');
            $shop_long = $request->input('shop_long');

            // ✅ Validate location inputs
            if (!is_numeric($ptt_lat) || !is_numeric($ptt_long) ||
                $ptt_lat < -90 || $ptt_lat > 90 || $ptt_long < -180 || $ptt_long > 180) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid latitude or longitude.'
                ], 400);
            }

$locationSet = DB::table('tbl_shop')->where('shop_id', $shop_id)->first();

if ($locationSet) {
    $updateData = [];

    if (is_null($locationSet->shop_lat) || $locationSet->shop_lat === '') {
        $updateData['shop_lat'] = $shop_lat;
        $locationSet->shop_lat = $shop_lat; // update for distance check
    }

    if (is_null($locationSet->shop_long) || $locationSet->shop_long === '') {
        $updateData['shop_long'] = $shop_long;
        $locationSet->shop_long = $shop_long; // update for distance check
    }

    if (!empty($updateData)) {
        DB::table('tbl_shop')->where('shop_id', $shop_id)->update($updateData);
    }
} else {
    DB::table('tbl_shop')->insert([
        'shop_id' => $shop_id,
        'shop_lat' => $shop_lat,
        'shop_long' => $shop_long,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $locationSet = (object)[
        'shop_lat' => $shop_lat,
        'shop_long' => $shop_long
    ];
}
                // Calculate distance between current and shop location
                $distance = $this->distance_map($locationSet->shop_lat, $locationSet->shop_long, $shop_lat, $shop_long);

                // 50-meter strict check
                if ($distance > 50) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'You are not in the correct shop location. Please come to your shop to proceed.'
                    ], 403);
                }
            

            // ✅ Validate images
            $request->validate([
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'signature' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            ]);

            // ✅ Compress and save images
            $imageUrl = $request->hasFile('image') 
                ? $this->compressAndSaveImage($request->file('image'), 'image', 120) 
                : null;

            $signatureUrl = $request->hasFile('signature') 
                ? $this->compressAndSaveImage($request->file('signature'), 'signature', 100) 
                : null;

            // ✅ Prepare data
            $insertArray = [
                'ptt_pt_id' => $pt_id,
                'ptt_po_id' => $po_id,
                'ptt_taken_q' => $ptt_taken_q,
                'ptt_taken_k' => $ptt_taken_k,
                'ptt_taken_g' => $ptt_taken_g,
                'ptt_user_id' => $ptt_user_id,
                'ptt_lat' => $ptt_lat,
                'ptt_long' => $ptt_long,
                'ptt_date' => now(),
                'image' => $imageUrl,
                'signature' => $signatureUrl,
            ];

            // ✅ DB transaction
            DB::transaction(function () use ($insertArray, $po_id, $pt_id) {
                DB::table('tbl_phase_three')->insert($insertArray);
                DB::table('tbl_phase_one')->where('po_id', $po_id)->update(['po_status' => '3']);
                DB::table('tbl_phase_two')->where('pt_id', $pt_id)->update(['pt_status' => '1']);
            });

            return response()->json([
                'status' => 1,
                'message' => 'successful..',
                'data' => 'Phase 3 completed successfully...'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compress image and save to public folder
     */
    private function compressAndSaveImage($file, $folder, $targetWidth)
    {
        $filename = $folder . '_' . uniqid() . '.jpg';
        $destinationPath = public_path("admin/upload/{$folder}/");

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $img = imagecreatefromstring(file_get_contents($file->getRealPath()));
        $width = imagesx($img);
        $height = imagesy($img);

        $targetHeight = (int)(($height / $width) * $targetWidth);
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        // Compress to < 2 KB
        $quality = 50;
        do {
            ob_start();
            imagejpeg($resized, null, $quality);
            $compressedData = ob_get_clean();
            $sizeKB = strlen($compressedData) / 1024;
            $quality -= 5;
        } while ($sizeKB > 2 && $quality > 0);

        file_put_contents($destinationPath . $filename, $compressedData);

        imagedestroy($img);
        imagedestroy($resized);

        return url("admin/upload/{$folder}/" . $filename);
    }

    /**
     * Calculate distance between two lat/long points in meters
     */
    private function distance_map($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344 * 1000; // meters
    }



    //===================




    public function getShopDetails(Request $request)
    {

        $inputData = $request->all();
        foreach ($inputData as $key => $value) {
            $$key = $value;
        }


        $result = DB::table('tbl_shop AS t2')
            ->join('tbl_zone AS t3', 't2.shop_zone_id', 't3.zone_id')
            ->join('tbl_district AS t4', 't3.zone_dist_id', 't4.dist_id')
            ->join('tbl_state AS t5', 't4.dist_state_id', 't5.state_id')
            ->where('t2.shop_id', $shop_id)
            ->select(['t2.shop_id', 't2.shop_name', 't2.shop_no', 't2.shop_owner', 't2.shop_add', 't3.zone_name', 't4.dist_name', 't5.state_name'])
            ->get();


        $returnArray = array();
        if ($result->isEmpty()) {
            $returnArray['status'] = 0;
            $returnArray['message'] = 'IncorrectNo data found.';
        } else {
            $returnArray['status'] = 1;
            $returnArray['message'] = 'Shop details.';
            $returnArray['data'] = $result;
        }
        return $returnArray;
    }

    //===================
    public function getHistory(Request $request)
    {
        try {
            // ✅ Get logged-in user ID from token (middleware)
            $user_id = $request->user_id;

            // ✅ Get shop_id, from_date, to_date from request
            $shop_id = $request->input('shop_id');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');

            // Validate required fields
            if (!$shop_id || !$from_date || !$to_date) {
                return response()->json([
                    'status' => 0,
                    'message' => 'shop_id, from_date and to_date are required.'
                ], 400);
            }

            // ✅ Verify that the shop belongs to logged-in user
            $loginRecord = DB::table('tbl_login')
                ->where('user_id', $user_id)
                ->where('user_ref_id', $shop_id)
                ->first();

            if (!$loginRecord) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Unauthorized: This shop does not belong to the logged-in user.'
                ], 403);
            }

            // ✅ Format dates for query
            $from_dateAA = date('Y-m-d 00:00:00', strtotime($from_date));
            $to_dateAA = date('Y-m-d 23:59:59', strtotime($to_date));

            // ✅ Fetch history using Query Builder
            $results = DB::table('tbl_phase_three as t0')
                ->join('tbl_phase_two as t1', 't0.ptt_pt_id', '=', 't1.pt_id')
                ->join('tbl_phase_one as t2', 't1.pt_po_id', '=', 't2.po_id')
                ->join('tbl_zone as t3', 't2.po_zone_id', '=', 't3.zone_id')
                ->join('tbl_driver as t7', 't2.po_driver_id', '=', 't7.driver_id')
                ->where('t1.pt_shop_id', $shop_id)
                ->whereBetween('t0.ptt_date', [$from_dateAA, $to_dateAA])
                ->orderByDesc('t0.ptt_id')
                ->select([
                    't1.pt_taken_q',
                    't1.pt_taken_k',
                    't1.pt_taken_g',
                    't0.ptt_taken_q',
                    't0.ptt_taken_k',
                    't0.ptt_taken_g',
                    't0.ptt_remain_q',
                    't0.ptt_remain_k',
                    't0.ptt_remain_g',
                    't0.image',
                    't0.signature',
                    't7.driver_name',
                    't7.driver_truck_no',
                    't3.zone_name'
                ])
                ->get();




            // ✅ Return response
            if ($results->isEmpty()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No data found.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 1,
                'message' => 'Phase three history result.',
                'data' => $results
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    //===================






}
