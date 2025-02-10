<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveApplication;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'employees' => 'required|array',
                'application_dates' => 'required',
                'leave_category_id' => 'required',
                'attachment' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
                'note' => 'nullable|max:255',
            ]);


            $employees = $request->employees;

            if (!is_array($employees)) {
                throw new Error('Employees must be an array of ids. Example: [1, 2, 4]');
            }

            $date = $request->date;
            $applicationDates = $request->application_dates;
            $leaveCategoryId = $request->leave_category_id;
            $applicationDaysCount = count(explode(',', $applicationDates));
            $note = $request->note;
            $approvalStatus = $request->approval_status;

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                // Uploading Image
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'timeoffs/leaves/' . $name;
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            // $applications = collect($employees)->map(function ($employeeId) use ($applicationDates, $date, $note, $approvalStatus) {
            //     return [
            //         'date' => $date,
            //         'application_dates' => $applicationDates,
            //         'note' => $note,
            //         'approval_status' => $approvalStatus,
            //         'employee_id' => $employeeId,
            //         'created_at' => Carbon::now()->toDateTimeString(),
            //         'updated_at' => Carbon::now()->toDateTimeString(),
            //     ];
            // })->values()->all();

            // return $applications;

            // $newApplications = SickApplication::create($applications);
            // DB::table('leave_applications')->insert($applications);

            collect($employees)->each(function ($employeeId) use ($applicationDates, $date, $note, $approvalStatus, $applicationDaysCount, $leaveCategoryId) {
                $applicationData = [
                    'date' => $date,
                    'application_dates' => $applicationDates,
                    'note' => $note,
                    'approval_status' => $approvalStatus,
                    'leave_category_id' => $leaveCategoryId,
                    'employee_id' => $employeeId,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];

                $newApplication = LeaveApplication::create($applicationData);

                if ($approvalStatus == 'approved') {
                    $attendances = [];
                    $explodedApplicationDates = explode(',', $applicationDates);
                    if (is_array($explodedApplicationDates)) {
                        foreach ($explodedApplicationDates as $applicationDate) {
                            $isValidDate = Carbon::canBeCreatedFromFormat($applicationDate, 'Y-m-d');
                            if ($isValidDate) {
                                array_push($attendances, [
                                    'employee_id' => $employeeId,
                                    'date' => $applicationDate,
                                    'status' => 'cuti',
                                    'approval_status' => 'approved',
                                    'leave_application_id' => $newApplication->id,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);
                            }
                        }
                    }

                    DB::table('attendances')->insert($attendances);

                    // Update employee's leave
                    // $leave = Leave::query()->where('employee_id', $employeeId)->first();
                    // $leave = Leave::firstOrCreate([
                    //     'employee_id' => $employeeId,
                    // ]);
                    // $totalTaken = $leave->taken + $applicationDaysCount;
                    // $leave->taken = $totalTaken > $leave->total ? $leave->total : $totalTaken;
                    // $leave->save();
                }
            });

            DB::commit();
            return response()->json([
                'message' => 'Data perusahaan telah tersimpan',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'employee_id' => 'required|integer',
                'application_dates' => 'required',
                'attachment' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
                'note' => 'nullable|max:255',
            ]);


            $employeeId = $request->employee_id;


            $date = $request->date;
            $applicationDates = $request->application_dates;
            $applicationDaysCount = count(explode(',', $applicationDates));
            $note = $request->note;
            $approvalStatus = $request->approval_status;

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                // Uploading Image
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'timeoffs/leaves/' . $name;
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            // $applications = collect($employees)->map(function ($employeeId) use ($applicationDates, $date, $note, $approvalStatus) {
            //     return [
            //         'date' => $date,
            //         'application_dates' => $applicationDates,
            //         'note' => $note,
            //         'approval_status' => $approvalStatus,
            //         'employee_id' => $employeeId,
            //         'created_at' => Carbon::now()->toDateTimeString(),
            //         'updated_at' => Carbon::now()->toDateTimeString(),
            //     ];
            // })->values()->all();

            // return $applications;

            // $newApplications = SickApplication::create($applications);
            // DB::table('leave_applications')->insert($applications);

            $applicationData = [
                'date' => $date,
                'application_dates' => $applicationDates,
                'note' => $note,
                'approval_status' => $approvalStatus,
                'employee_id' => $employeeId,
                // 'created_at' => Carbon::now()->toDateTimeString(),
                // 'updated_at' => Carbon::now()->toDateTimeString(),
            ];

            $newApplication = LeaveApplication::where('id', $id)->update($applicationData);

            if ($approvalStatus == 'approved') {
                $attendances = [];
                $explodedApplicationDates = explode(',', $applicationDates);
                if (is_array($explodedApplicationDates)) {
                    foreach ($explodedApplicationDates as $applicationDate) {
                        $isValidDate = Carbon::canBeCreatedFromFormat($applicationDate, 'Y-m-d');
                        if ($isValidDate) {
                            array_push($attendances, [
                                'employee_id' => $employeeId,
                                'date' => $applicationDate,
                                'status' => 'cuti',
                                'approval_status' => 'approved',
                                'leave_application_id' => $id,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString(),
                            ]);
                        }
                    }
                }

                DB::table('attendances')->insert($attendances);

                // Update employee's leave
                // $leave = Leave::query()->where('employee_id', $employeeId)->first();
                $leave = Leave::firstOrCreate([
                    'employee_id' => $employeeId,
                ]);
                $totalTaken = $leave->taken + $applicationDaysCount;
                $leave->taken = $totalTaken > $leave->total ? $leave->total : $totalTaken;
                $leave->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Data perusahaan telah tersimpan',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
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
        DB::beginTransaction();
        try {
            $application = LeaveApplication::findOrFail($id);

            $applicationDaysCount = count(explode(',', $application->application_dates));
            $employeeId = $application->employee_id;

            if ($application->approval_status == 'approved') {
                $leave = Leave::query()->where('employee_id', $employeeId)->first();
                $changeTaken = $leave->taken - $applicationDaysCount;
                $finalTaken = $changeTaken;
                // If change taken greater than employee's leave total
                // TL: Jika perubahan lebih besar dari jatah cuti pegawai 
                if ($changeTaken > $leave->total) {
                    $finalTaken = $leave->total;
                } else {
                    // If change taken lower than zero
                    // TL: Jika perubahan lebih kecil dari nol 
                    if ($changeTaken < 0) {
                        $finalTaken = 0;
                    }
                }
                if ($finalTaken !== null) {
                    $leave->taken =  $finalTaken;
                }
                $leave->save();
            }

            $application->delete();


            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve application.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $application = LeaveApplication::find($id);

            $employeeId = $application->employee_id;

            if ($application->approval_status !== 'pending') {
                throw new Error('pengajuan ini sudah dikonfirmasi');
            }

            $application->approval_status = 'approved';
            $application->confirmed_by = Auth::user()->employee->id ?? null;
            $application->confirmed_at = date("Y-m-d H:i:s");
            $application->save();

            $attendances = [];
            $applicationDates = explode(',', $application->application_dates);
            if (is_array($applicationDates)) {
                foreach ($applicationDates as $applicationDate) {
                    $isValidDate = Carbon::canBeCreatedFromFormat($applicationDate, 'Y-m-d');
                    if ($isValidDate) {
                        array_push($attendances, [
                            'employee_id' => $employeeId,
                            'date' => $applicationDate,
                            'status' => 'cuti',
                            'approval_status' => 'approved',
                            'leave_application_id' => $id,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]);
                    }
                }
            }

            // return response()->json([
            //     'data' => $attendances,
            // ]);

            DB::table('attendances')->insert($attendances);

            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve application.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        DB::beginTransaction();
        try {
            $application = LeaveApplication::find($id);

            if ($application->approval_status !== 'pending') {
                throw new Error('pengajuan ini sudah dikonfirmasi');
            }

            $application->approval_status = 'rejected';
            $application->confirmed_by = Auth::user()->employee->id ?? null;
            $application->confirmed_at = date("Y-m-d H:i:s");
            $application->save();

            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
