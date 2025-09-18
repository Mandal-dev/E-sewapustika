<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PayMatrixImportController extends Controller
{
public function import(Request $request)
{
    $user = Session::get('user');
    if (!$user) return redirect('/');

    $request->validate([
        'file' => 'required|mimes:csv,txt'
    ]);

    $file = $request->file('file');
    $path = $file->getPathname();

    try {
        $rows = array_map('str_getcsv', file($path));

        if (empty($rows)) {
            return back()->with('error', 'File is empty');
        }

        $headers = $rows[0];
        $rows = array_slice($rows, 1);

        $successCount = 0;
        $failures = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $levelNo = trim($row[0] ?? '');

            if (!$levelNo) {
                $failures[] = "Row $rowNumber: Level missing.";
                continue;
            }

            // Insert or get Level
            $level = DB::table('levels')->where('level_no', $levelNo)->first();
            $level_id = $level ? $level->id : DB::table('levels')->insertGetId(['level_no' => $levelNo]);

            // Loop through each stage column
            foreach ($row as $colIndex => $value) {
                if ($colIndex == 0) continue;
                if ($value === null || $value === '') continue;

                $stageCode = $headers[$colIndex];

                // Insert stage if not exists
                $stage = DB::table('stages')->where('stage_code', $stageCode)->first();
                if (!$stage) {
                    $stage_id = DB::table('stages')->insertGetId(['stage_code' => $stageCode]);
                } else {
                    $stage_id = $stage->id;
                }

                // Insert or update pay_matrix
                $exists = DB::table('pay_matrix')
                    ->where('level_id', $level_id)
                    ->where('stage_id', $stage_id)
                    ->first();

                if ($exists) {
                    DB::table('pay_matrix')
                        ->where('id', $exists->id)
                        ->update(['amount' => $value]);
                } else {
                    DB::table('pay_matrix')->insert([
                        'level_id' => $level_id,
                        'stage_id' => $stage_id,
                        'amount'   => $value,
                    ]);
                }

                $successCount++;
            }
        }

        $message = "$successCount Pay Matrix entries imported successfully.";
        if (!empty($failures)) {
            $message .= " Some rows failed:<br>" . implode("<br>", $failures);
            return back()->with('error', $message);
        }

        return back()->with('success', $message);

    } catch (\Exception $e) {
        return back()->with('error', 'Error importing Pay Matrix: ' . $e->getMessage());
    }
}





    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define stages (S-1 to S-31)
        $stages = [];
        for ($i = 1; $i <= 31; $i++) {
            $stages[] = "S-$i";
        }

        // Define headers: Level + stages
        $headers = array_merge(['Level'], $stages);

        // Set headers in first row
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Optional: prefill levels 1–12 in first column
        for ($level = 1; $level <= 12; $level++) {
            $sheet->setCellValue('A' . ($level + 1), $level);
        }

        // Prepare file for download
        $filename = 'pay_matrix_template.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}


