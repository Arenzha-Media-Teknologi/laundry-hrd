<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WarningLetter;
use App\Models\WarningLetterAttachment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

class WarningLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warningLetters = WarningLetter::with(['employee', 'signatoryEmployee', 'attachments'])->get();

        return view('warning-letters.index', [
            'warningLetters' => $warningLetters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::where('active', 1)->with(['activeCareer.jobTitle', 'office.division.company'])->get();

        return view('warning-letters.create', [
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $employeeId = $request->employee_id;
            $effectiveStartDate = $request->effective_start_date;
            $effectiveEndDate = $request->effective_end_date;
            $type = $request->type;
            $description = $request->description;
            $signatory = $request->signatory;

            $validated = $request->validate([
                'employee_id' => 'required|integer|exists:employees,id',
                'effective_start_date' => 'nullable|date',
                'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
                'type' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'signatory' => 'nullable|integer|exists:employees,id',
                'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
            ]);

            $warningLetter = new WarningLetter();
            $warningLetter->employee_id = $employeeId;
            $warningLetter->number = $this->generateWarningLetterNumber($type, $employeeId, $effectiveStartDate);
            $warningLetter->effective_start_date = $effectiveStartDate;
            $warningLetter->effective_end_date = $effectiveEndDate;
            $warningLetter->type = $type;
            $warningLetter->description = $description;
            $warningLetter->signatory = $signatory;
            $warningLetter->save();

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $this->saveAttachments($warningLetter->id, $request->file('attachments'));
            }

            $newWarningLetter = WarningLetter::with(['employee', 'signatoryEmployee', 'attachments'])->find($warningLetter->id);

            return response()->json([
                'message' => 'Data surat peringatan telah tersimpan',
                'data' => $newWarningLetter,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $warningLetter = WarningLetter::with(['employee', 'signatoryEmployee.activeCareer.jobTitle', 'signatoryEmployee.office.division.company', 'attachments'])->findOrFail($id);

        return view('warning-letters.show', [
            'warningLetter' => $warningLetter,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $warningLetter = WarningLetter::with(['employee', 'signatoryEmployee.activeCareer.jobTitle', 'signatoryEmployee.office.division.company', 'attachments'])->findOrFail($id);
        $employees = Employee::where('active', 1)->with(['activeCareer.jobTitle', 'office.division.company'])->get();

        return view('warning-letters.edit', [
            'warningLetter' => $warningLetter,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $employeeId = $request->employee_id;
            $effectiveStartDate = $request->effective_start_date;
            $effectiveEndDate = $request->effective_end_date;
            $type = $request->type;
            $description = $request->description;
            $signatory = $request->signatory;

            $warningLetter = WarningLetter::with(['employee', 'signatoryEmployee'])->find($id);

            $validated = $request->validate([
                'employee_id' => 'required|integer|exists:employees,id',
                'effective_start_date' => 'nullable|date',
                'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
                'type' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'signatory' => 'nullable|integer|exists:employees,id',
                'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
            ]);

            $warningLetter->employee_id = $employeeId;
            $warningLetter->effective_start_date = $effectiveStartDate;
            $warningLetter->effective_end_date = $effectiveEndDate;
            $warningLetter->type = $type;
            $warningLetter->description = $description;
            $warningLetter->signatory = $signatory;
            $warningLetter->save();

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $this->saveAttachments($warningLetter->id, $request->file('attachments'));
            }

            $warningLetter->load(['attachments']);

            return response()->json([
                'message' => 'Data surat peringatan telah tersimpan',
                'data' => $warningLetter,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $warningLetter = WarningLetter::findOrFail($id);
            $warningLetter->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auto-create warning letter from "Pegawai Terlambat Bulan Berjalan" dashboard widget.
     *
     * Rules:
     * - three_times_late: create SP1 for 3 months, description mentions 3 times late
     * - two_times_late_sp1: create SP1 for 2 months, description mentions 2 times late
     * - one_time_late_sp2: create SP3 for 1 day, description mentions 1 time late, and deactivate employee immediately
     */
    public function autoFromLate(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'late_count' => 'required|integer|min:1',
            'dates' => 'nullable|array',
            'dates.*' => 'nullable|date',
            'mode' => 'required|string|in:three_times_late,two_times_late_sp1,one_time_late_sp2',
        ]);

        $employeeId = (int) $validated['employee_id'];
        $lateCount = (int) $validated['late_count'];
        $mode = $validated['mode'];
        $dates = collect($validated['dates'] ?? [])->filter()->values()->all();

        $today = Carbon::now()->startOfDay();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        return DB::transaction(function () use ($employeeId, $lateCount, $mode, $dates, $today, $startOfMonth, $endOfMonth) {
            $employee = Employee::lockForUpdate()->findOrFail($employeeId);

            $type = null;
            $effectiveStartDate = $today->toDateString();
            $effectiveEndDate = null;
            $description = null;

            if ($mode === 'three_times_late') {
                $type = 'sp1';
                $effectiveEndDate = $today->copy()->addMonthsNoOverflow(3)->toDateString();
                $description = 'Terlambat ' . $lateCount . ' kali pada bulan ' . Carbon::now()->locale('id')->isoFormat('MMMM YYYY') . '.';
            } elseif ($mode === 'two_times_late_sp1') {
                $type = 'sp1';
                $effectiveEndDate = $today->copy()->addMonthsNoOverflow(2)->toDateString();
                $description = 'Terlambat ' . $lateCount . ' kali pada bulan ' . Carbon::now()->locale('id')->isoFormat('MMMM YYYY') . '.';
            } elseif ($mode === 'one_time_late_sp2') {
                $type = 'sp3';
                $effectiveEndDate = $today->copy()->addDay()->toDateString();
                $description = 'Terlambat ' . $lateCount . ' kali saat dalam masa SP2 pada bulan ' . Carbon::now()->locale('id')->isoFormat('MMMM YYYY') . '.';
            }

            if (!empty($dates)) {
                $dateText = collect($dates)->map(function ($d) {
                    try {
                        return Carbon::parse($d)->locale('id')->isoFormat('DD MMM');
                    } catch (\Throwable $th) {
                        return null;
                    }
                })->filter()->implode(', ');

                if (!empty($dateText)) {
                    $description .= ' Tanggal terlambat: ' . $dateText . '.';
                }
            }

            // Guard: do not create duplicate letter of same type in current month
            $existsThisMonth = WarningLetter::where('employee_id', $employeeId)
                ->where('type', $type)
                ->whereBetween('effective_start_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->exists();

            if ($existsThisMonth) {
                return response()->json([
                    'message' => 'Surat peringatan (' . strtoupper($type) . ') untuk pegawai ini sudah dibuat di bulan berjalan.',
                ], 422);
            }

            // Guard: prevent creating SP1 if an active SP1 exists
            if ($type === 'sp1') {
                $hasActiveSp1 = WarningLetter::where('employee_id', $employeeId)
                    ->where('type', 'sp1')
                    ->where('effective_start_date', '<=', $today->toDateString())
                    ->where('effective_end_date', '>=', $today->toDateString())
                    ->exists();
                if ($hasActiveSp1) {
                    return response()->json([
                        'message' => 'Pegawai masih memiliki SP1 aktif.',
                    ], 422);
                }
            }

            // Guard: SP3 requires active SP2
            if ($type === 'sp3') {
                $hasActiveSp2 = WarningLetter::where('employee_id', $employeeId)
                    ->where('type', 'sp2')
                    ->where('effective_start_date', '<=', $today->toDateString())
                    ->where('effective_end_date', '>=', $today->toDateString())
                    ->exists();
                if (!$hasActiveSp2) {
                    return response()->json([
                        'message' => 'SP3 hanya dapat dibuat jika pegawai masih dalam masa SP2.',
                    ], 422);
                }
            }

            $warningLetter = new WarningLetter();
            $warningLetter->employee_id = $employeeId;
            $warningLetter->number = $this->generateWarningLetterNumber($type, $employeeId, $effectiveStartDate);
            $warningLetter->effective_start_date = $effectiveStartDate;
            $warningLetter->effective_end_date = $effectiveEndDate;
            $warningLetter->type = $type;
            $warningLetter->description = $description;
            $warningLetter->signatory = null;
            $warningLetter->save();

            // Mark attendances as counted for warning letter
            // Only mark attendances that haven't been marked yet (both warning_letter_created_at and warning_letter_type are null)
            if (!empty($dates)) {
                Attendance::where('employee_id', $employeeId)
                    ->where('time_late', '>', 0)
                    ->where('status', 'hadir')
                    ->whereIn('date', $dates)
                    ->whereNull('warning_letter_created_at')
                    ->whereNull('warning_letter_type')
                    ->update([
                        'warning_letter_created_at' => Carbon::now(),
                        'warning_letter_type' => $type
                    ]);
            } else {
                // If no specific dates provided, mark all late attendances in current month that haven't been marked
                Attendance::where('employee_id', $employeeId)
                    ->where('time_late', '>', 0)
                    ->where('status', 'hadir')
                    ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->whereNull('warning_letter_created_at')
                    ->whereNull('warning_letter_type')
                    ->update([
                        'warning_letter_created_at' => Carbon::now(),
                        'warning_letter_type' => $type
                    ]);
            }

            // If SP3: deactivate employee immediately
            if ($type === 'sp3') {
                $employee->active = 0;
                $employee->inactive_at = Carbon::now();
                $employee->save();
            }

            $warningLetter->load(['employee', 'signatoryEmployee', 'attachments']);

            return response()->json([
                'message' => 'Surat peringatan berhasil dibuat otomatis (' . strtoupper($type) . ').',
                'data' => $warningLetter,
            ]);
        });
    }

    /**
     * Generate warning letter number based on format: SP01/PERUSAHAAN/BULAN-TAHUN/HRD/URUT
     *
     * @param string|null $type
     * @param int $employeeId
     * @param string|null $effectiveStartDate
     * @return string
     */
    private function generateWarningLetterNumber($type, $employeeId, $effectiveStartDate = null)
    {
        // Get employee with company relationship
        $employee = Employee::with(['office.division.company'])->find($employeeId);

        if (!$employee || !$employee->office || !$employee->office->division || !$employee->office->division->company) {
            throw new Exception('Employee company information not found');
        }

        // Format SP type (sp1 -> SP01, sp2 -> SP02, sp3 -> SP03)
        $spType = strtoupper($type ?? 'sp1');
        if (preg_match('/sp(\d+)/i', $spType, $matches)) {
            $spNumber = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $spType = 'SP' . $spNumber;
        } else {
            $spType = 'SP01'; // Default
        }

        // Get company name (uppercase, replace spaces with dashes if needed)
        $companyName = strtoupper(str_replace(' ', '-', $employee->office->division->company->name));

        // Get month and year from effective_start_date or current date
        $date = $effectiveStartDate ? Carbon::parse($effectiveStartDate) : Carbon::now();
        $monthYear = $date->format('m-Y'); // Format: MM-YYYY (e.g., 01-2024)

        // Find the last sequence number for this combination (SP type, company, month-year)
        $lastWarningLetter = WarningLetter::where('type', $type ?? 'sp1')
            ->where('number', 'like', $spType . '/' . $companyName . '/' . $monthYear . '/HRD/%')
            ->orderBy('number', 'desc')
            ->first();

        // Extract sequence number from last warning letter
        $sequence = 1;
        if ($lastWarningLetter && $lastWarningLetter->number) {
            $parts = explode('/', $lastWarningLetter->number);
            if (count($parts) >= 5 && is_numeric($parts[4])) {
                $sequence = (int)$parts[4] + 1;
            }
        }

        // Format sequence with leading zeros (at least 3 digits)
        $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

        // Generate full number: SP01/PERUSAHAAN/BULAN-TAHUN/HRD/URUT
        return $spType . '/' . $companyName . '/' . $monthYear . '/HRD/' . $formattedSequence;
    }

    /**
     * Print payslip
     */
    public function print($id)
    {
        $warningLetter = WarningLetter::with(['employee.activeCareer', 'signatoryEmployee.activeCareer.jobTitle', 'signatoryEmployee.office.division.company'])->findOrFail($id);

        $data = [
            'warning_letter' => $warningLetter,
        ];

        $views = [
            'sp1' => 'warning-letters.print-sp1',
            'sp2' => 'warning-letters.print-sp2',
            'sp3' => 'warning-letters.print-sp3',
        ];

        $view = $views[$warningLetter->type] ?? null;

        if ($view == null) {
            abort(404);
        }

        $pdf = PDF::loadView($view, $data, [], [
            'margin_top' => 30,
            'margin_left' => 25,
            'margin_right' => 25,
        ]);
        return $pdf->stream();
    }

    /**
     * Save attachments for warning letter
     *
     * @param int $warningLetterId
     * @param array $files
     * @return void
     */
    private function saveAttachments($warningLetterId, $files)
    {
        $path = 'warning-letters/attachments/';

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            $filePath = $path . $fileName;

            // Upload to S3
            Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');

            // Get full URL from S3
            $fileUrl = Storage::disk('s3')->url($filePath);

            WarningLetterAttachment::create([
                'warning_letter_id' => $warningLetterId,
                'file_name' => $fileName,
                'file_path' => $fileUrl,
                'original_name' => $originalName,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }

    /**
     * Delete attachment
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAttachment($id)
    {
        try {
            $attachment = WarningLetterAttachment::findOrFail($id);

            // Extract path from URL if it's a full URL
            $filePath = $this->extractPathFromUrl($attachment->file_path);

            // Delete file from S3
            if (Storage::disk('s3')->exists($filePath)) {
                Storage::disk('s3')->delete($filePath);
            }

            // Delete record
            $attachment->delete();

            return response()->json([
                'message' => 'Lampiran berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus lampiran - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download attachment
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadAttachment($id)
    {
        try {
            $attachment = WarningLetterAttachment::findOrFail($id);

            // Extract path from URL if it's a full URL
            $filePath = $this->extractPathFromUrl($attachment->file_path);

            if (!Storage::disk('s3')->exists($filePath)) {
                abort(404, 'File not found');
            }

            // Get file from S3
            $fileContent = Storage::disk('s3')->get($filePath);
            $mimeType = $attachment->mime_type ?? 'application/octet-stream';

            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $attachment->original_name . '"');
        } catch (Exception $e) {
            abort(404, 'File not found');
        }
    }

    /**
     * Extract path from URL or return path as is
     *
     * @param string $urlOrPath
     * @return string
     */
    private function extractPathFromUrl($urlOrPath)
    {
        // If it's already a path (doesn't start with http), return as is
        if (!preg_match('/^https?:\/\//', $urlOrPath)) {
            return $urlOrPath;
        }

        // Extract path from URL
        $parsedUrl = parse_url($urlOrPath);
        return ltrim($parsedUrl['path'] ?? '', '/');
    }
}
