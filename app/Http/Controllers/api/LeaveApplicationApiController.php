<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\LeaveApplication;
use App\Models\LeaveCategory;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class LeaveApplicationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $whereClause = $request->query();
        try {
            $sickApplications = LeaveApplication::with(['category'])->where($whereClause)->get();
            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $sickApplications,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => true,
                'code' => 500,
                'errors' => $e
            ], 500);
        }
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
        try {
            $employeeId = $request->employee_id;
            $date = $request->date;
            $applicationDates = $request->application_dates;
            $approvalStatus = $request->approval_status;
            $leaveCategoryId = $request->leave_category_id;
            $note = $request->note;

            $validated = $request->validate([
                'employee_id' => 'required|numeric',
                'date' => 'required|date',
                'application_dates' => [
                    'required',
                    Rule::unique('sick_applications')->where(function ($query) use ($applicationDates, $employeeId) {
                        return $query->where('application_dates', 'like', '%' . $applicationDates . '%')->whereIn('employee_id', [$employeeId]);
                    }),
                    'max:255'
                ],
                'approval_status' => [
                    Rule::in(['pending', 'approved', 'rejected'])
                ],
                'leave_category_id' => 'required|numeric',
                'note' => 'max:255',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // -----------------
            $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', date('Y'))->get();
            $leaveQuota = 12;
            $employee = Employee::with(['leaveApplications' => function ($q) {
                $q->whereHas('category', function ($q2) {
                    $q2->where('type', 'annual_leave');
                })->where('approval_status', 'approved');
            }])->findOrFail($employeeId);

            $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                return explode(',', $leaveApplicationDates);
            })->all();

            $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);
            // $leavePeriod = new CarbonPeriod('2022-01-01', '2022-12-30');
            // $leavePeriodDates = [];
            // foreach ($leavePeriod as $key => $date) {
            //     $leavePeriodDates[] = $date->format('Y-m-d');
            // }
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-t');

            $leaveApplicationsCount = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($startDate, $endDate) {
                return $date >= $startDate && $date <= $endDate;
            })->count();

            $massLeavesCount = collect($massLeaves)->count();

            $takenLeave = $leaveApplicationsCount + $massLeavesCount;
            $leave = [
                'total' => $leaveQuota,
                'taken' => $takenLeave,
            ];

            $remainingLeaves = $leave['total'] - $leave['taken'];

            $selectedLeaveCategory = LeaveCategory::findOrFail($leaveCategoryId);
            $applicationDatesCount = count(explode(',', $applicationDates));

            if ($selectedLeaveCategory->type == 'annual_leave') {
                if ($applicationDatesCount > $remainingLeaves) {
                    throw new Error('Jumlah hari yang diambil melebihi batas pengambilan');
                }
            } else {
                $maxDay = $selectedLeaveCategory->max_day;
                if ($applicationDatesCount > $maxDay) {
                    throw new Error('Jumlah hari yang diambil melebihi batas pengambilan');
                }
            }

            // ---------------


            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                // Compress Image
                $imageWidth = Image::make($file->getRealPath())->width();
                $ratio = 50 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '-clockout-' . $file->getClientOriginalName();
                $filePath = 'timeoffs/attachments/sicks_' . $name;
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $application = new LeaveApplication();
            $application->date = $date;
            $application->application_dates = $applicationDates;
            $application->note = $note;
            $application->approval_status = $approvalStatus;
            $application->employee_id = $employeeId;
            $application->leave_category_id = $leaveCategoryId;
            $application->attachment = $urlPath;
            $application->save();

            $newApplication = LeaveApplication::with(['category'])->find($application->id);

            return response()->json([
                'message' => 'Data pengajuan telah tersimpan',
                'code' => 200,
                'data' => $newApplication,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
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
        try {
            $leaveApplication = LeaveApplication::with(['category', 'employee', 'confirmedByEmployee'])->find($id);

            return response()->json([
                'message' => 'OK',
                'data' => $leaveApplication,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
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
        try {
            $employeeId = $request->employee_id;
            $date = $request->date;
            $applicationDates = $request->application_dates;
            $approvalStatus = $request->approval_status;
            $leaveCategoryId = $request->leave_category_id;
            $note = $request->note;

            $validated = $request->validate([
                'employee_id' => 'required|numeric',
                'date' => 'required|date',
                'application_dates' => [
                    'required',
                    Rule::unique('sick_applications')->where(function ($query) use ($applicationDates, $employeeId) {
                        return $query->where('application_dates', 'like', '%' . $applicationDates . '%')->whereIn('employee_id', [$employeeId]);
                    }),
                    'max:255'
                ],
                'approval_status' => [
                    Rule::in(['pending', 'approved', 'rejected'])
                ],
                'leave_category_id' => 'required|numeric',
                'note' => 'max:255',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $application = LeaveApplication::find($id);
            if (isset($application->approval_status) && $application->approval_status !== 'pending') {
                throw new Error('Pengajuan sudah dikonfirmasi');
            }

            // -----------------
            $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', date('Y'))->get();
            $leaveQuota = 12;
            $employee = Employee::with(['leaveApplications' => function ($q) {
                $q->whereHas('category', function ($q2) {
                    $q2->where('type', 'annual_leave');
                })->where('approval_status', 'approved');
            }])->findOrFail($employeeId);

            $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                return explode(',', $leaveApplicationDates);
            })->all();

            $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);
            // $leavePeriod = new CarbonPeriod('2022-01-01', '2022-12-30');
            // $leavePeriodDates = [];
            // foreach ($leavePeriod as $key => $date) {
            //     $leavePeriodDates[] = $date->format('Y-m-d');
            // }
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-t');

            $leaveApplicationsCount = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($startDate, $endDate) {
                return $date >= $startDate && $date <= $endDate;
            })->count();

            $massLeavesCount = collect($massLeaves)->count();

            $takenLeave = $leaveApplicationsCount + $massLeavesCount;
            $leave = [
                'total' => $leaveQuota,
                'taken' => $takenLeave,
            ];

            $remainingLeaves = $leave['total'] - $leave['taken'];

            $selectedLeaveCategory = LeaveCategory::findOrFail($leaveCategoryId);
            $applicationDatesCount = count(explode(',', $applicationDates));

            if ($selectedLeaveCategory->type == 'annual_leave') {
                if ($applicationDatesCount > $remainingLeaves) {
                    throw new Error('Jumlah hari yang diambil melebihi batas pengambilan');
                }
            } else {
                $maxDay = $selectedLeaveCategory->max_day;
                if ($applicationDatesCount > $maxDay) {
                    throw new Error('Jumlah hari yang diambil melebihi batas pengambilan');
                }
            }

            // ---------------


            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                // Compress Image
                $imageWidth = Image::make($file->getRealPath())->width();
                $ratio = 50 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '-clockout-' . $file->getClientOriginalName();
                $filePath = 'timeoffs/attachments/sicks_' . $name;
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $application->date = $date;
            $application->application_dates = $applicationDates;
            $application->note = $note;
            $application->approval_status = $approvalStatus;
            $application->employee_id = $employeeId;
            $application->leave_category_id = $leaveCategoryId;
            $application->attachment = $urlPath;
            $application->save();

            return response()->json([
                'message' => 'Data pengajuan telah tersimpan',
                'code' => 200,
                'data' => $application,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
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
            $application->delete();


            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus',
                'code' => 200,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }
}
