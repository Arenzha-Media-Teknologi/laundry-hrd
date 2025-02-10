<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\SickApplication;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SickApplicationController extends Controller
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
                'attachment' => 'nullable|mimes:jpeg,png,jpg,pdf|max:2048',
                'note' => 'nullable|max:255',
            ]);

            $employees = $request->employees;
            $date = $request->date;
            $applicationDates = $request->application_dates;
            $note = $request->note;
            $approvalStatus = $request->approval_status;

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                // Uploading Image
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'timeoffs/sicks/' . $name;
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            collect($employees)->each(function ($employeeId) use ($applicationDates, $date, $note, $approvalStatus) {
                $applicationData = [
                    'date' => $date,
                    'application_dates' => $applicationDates,
                    'note' => $note,
                    'approval_status' => $approvalStatus,
                    'employee_id' => $employeeId,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];

                $newApplication = SickApplication::create($applicationData);

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
                                    'status' => 'sakit',
                                    'approval_status' => 'approved',
                                    'sick_application_id' => $newApplication->id,
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);
                            }
                        }
                    }

                    DB::table('attendances')->insert($attendances);
                }
            });

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

            // DB::table('sick_applications')->insert($applications);

            DB::commit();
            return response()->json([
                'message' => 'Data perusahaan telah tersimpan',
                // 'data' => [],
            ]);
        } catch (Throwable $e) {
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
            $note = $request->note;
            $approvalStatus = $request->approval_status;

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                // Uploading Image
                $name = time() . '_' . $file->getClientOriginalName();
                $filePath = 'timeoffs/sicks/' . $name;
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $applicationData = [
                'date' => $date,
                'application_dates' => $applicationDates,
                'note' => $note,
                'approval_status' => $approvalStatus,
                'employee_id' => $employeeId,
                // 'created_at' => Carbon::now()->toDateTimeString(),
                // 'updated_at' => Carbon::now()->toDateTimeString(),
            ];

            $newApplication = SickApplication::where('id', $id)->update($applicationData);

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
                                'status' => 'sakit',
                                'approval_status' => 'approved',
                                'sick_application_id' => $id,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString(),
                            ]);
                        }
                    }
                }

                DB::table('attendances')->insert($attendances);
            }

            DB::commit();
            return response()->json([
                'message' => 'Data perusahaan telah tersimpan',
                // 'data' => [],
            ]);
        } catch (Throwable $e) {
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
        try {
            $application = SickApplication::findOrFail($id);
            $application->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
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
            $application = SickApplication::find($id);

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
                            'status' => 'sakit',
                            'approval_status' => 'approved',
                            'sick_application_id' => $id,
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
            $application = SickApplication::find($id);

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
