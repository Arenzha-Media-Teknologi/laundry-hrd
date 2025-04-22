<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\Credential;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\LeaveApplication;
use App\Models\SickApplication;
use App\Models\WorkingPattern;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $employees = [];
        $whereClause = $request->query();
        try {
            $employees = Employee::with(['activeCareer.jobTitle'])->where($whereClause)->get();
            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $employees,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to get data',
                'error' => true,
                'code' => 400,
                'errors' => $e->getMessage(),
            ], 400);
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
        //
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
            $employee = Employee::with([
                'activeCareer.jobTitle',
                'office.division.company',
                'bankAccounts',
                'credential',
                'activeWorkingPatterns' => function ($q) {
                    $q->with(['items']);
                },
                'attendances' => function ($q) {
                    $date = date('Y-m-d');
                    $q->with(['workingPattern' => function ($q2) {
                        $q2->with(['items']);
                    }])->where('date', $date)->where('clock_out_time', null)->orderBy('created_at')->limit(1);
                }
            ])->findOrFail($id);

            // return $todayLastClockOut->workingPattern;

            $employee->active_working_pattern_id = $employee->activeWorkingPatterns[0]->id ?? "";
            // $employee->active_working_pattern_id = "";

            $activeWorkingPatternItems = $employee->activeWorkingPatterns[0]->items ?? [];
            $todayClockInTime = null;
            $todayClockOutTime = null;
            $todayDayOrder = Carbon::now()->dayOfWeekIso;
            $todayWorkingPatternItem = collect($activeWorkingPatternItems)->where('order', $todayDayOrder)->first();
            if (isset($todayWorkingPatternItem)) {
                $todayClockInTime = date('Y-m-d') . ' ' . ($todayWorkingPatternItem->clock_in ?? "07:00:00");
                $todayClockOutTime = date('Y-m-d') . ' ' . ($todayWorkingPatternItem->clock_out ?? "17:00:00");
            }

            $todayLastClockIn = $employee->attendances[0] ?? null;
            $isLongShiftAvailable = false;
            $longShiftWorkingPatternId = null;

            $longShiftWorkingPattern = WorkingPattern::with(['items'])->where('time', 'siang')->where('division', 'aerplus')->first();


            if (isset($todayLastClockIn)) {
                $todayLastClockInWorkingPatternTime = $todayLastClockIn->workingPattern->time ?? null;
                $todayLastClockInWorkingPatternDivision = $todayLastClockIn->workingPattern->division ?? null;
                if ($todayLastClockInWorkingPatternTime == 'pagi' && $todayLastClockInWorkingPatternDivision == 'aerplus') {
                    $todayDayOrder = Carbon::now()->dayOfWeekIso;
                    $todayWorkingPatternItem = collect($todayLastClockIn->workingPattern->items)->where('order', $todayDayOrder)->first();

                    // Long Shift Working Pattern ----------------
                    $todayLongShiftWorkingPatternItem = collect($longShiftWorkingPattern->items)->where('order', $todayDayOrder)->first();
                    // ----------------

                    if (isset($todayWorkingPatternItem)) {
                        // $workingPatternItemClockOut = date('Y-m-d') . ' ' . $todayWorkingPatternItem->clock_out;
                        // $workingPatternItemClockOut = date('Y-m-d 11:00:00');

                        $workingPatternItemClockOut = date('Y-m-d') . ' ' . $todayLongShiftWorkingPatternItem->clock_out;
                        $todayTime = Carbon::now()->toDateTimeString();
                        if ($todayTime > $workingPatternItemClockOut) {
                            $isLongShiftAvailable = true;
                        }
                    }
                    // $isLongShiftAvailable = true;
                }
                // if($todayLastClockOut)
            }

            if (isset($longShiftWorkingPattern) && $isLongShiftAvailable) {
                $longShiftWorkingPatternId = $longShiftWorkingPattern->id;
            }

            $employee->is_long_shift_available = $isLongShiftAvailable;
            $employee->long_shift_working_pattern_id = $longShiftWorkingPatternId;

            $employee->today_clock_in_time = $todayClockInTime;
            $employee->today_clock_out_time = $todayClockOutTime;

            $isAerplusEmployee = false;

            $employeeDivisionId = $employee->office->division_id ?? null;

            $AERPLUS_DIVISION_ID = 12;

            if (isset($employeeDivisionId) && $employeeDivisionId == $AERPLUS_DIVISION_ID) {
                $isAerplusEmployee = true;
            }
            // $isAerplusEmployee = false;
            $employee->is_aerplus_employee = $isAerplusEmployee;

            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $employee,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to get data',
                'error' => true,
                'code' => 400,
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    public function showV2($id)
    {
        try {
            $employee = Employee::with([
                'activeCareer.jobTitle.designation.department',
                'office.division.company',
                'bankAccounts',
                'credential',
                'activeWorkingPatterns' => function ($q) {
                    $q->with(['items']);
                },
                'attendances' => function ($q) {
                    $date = date('Y-m-d');
                    $q->where('date', $date)->orderBy('id', 'DESC');
                }
            ])->findOrFail($id);

            // return $todayLastClockOut->workingPattern;

            $employee->active_working_pattern_id = $employee->activeWorkingPatterns[0]->id ?? "";
            // $employee->active_working_pattern_id = "";

            $activeWorkingPatternItems = $employee->activeWorkingPatterns[0]->items ?? [];
            $todayClockInTime = null;
            $todayClockOutTime = null;
            $todayDayOrder = Carbon::now()->dayOfWeekIso;
            $todayWorkingPatternItem = collect($activeWorkingPatternItems)->where('order', $todayDayOrder)->first();
            if (isset($todayWorkingPatternItem)) {
                $todayClockInTime = date('Y-m-d') . ' ' . ($todayWorkingPatternItem->clock_in ?? "07:00:00");
                $todayClockOutTime = date('Y-m-d') . ' ' . ($todayWorkingPatternItem->clock_out ?? "17:00:00");
            }

            $todayLastClockIn = $employee->attendances[0] ?? null;
            $isLongShiftAvailable = false;
            $longShiftWorkingPatternId = null;

            $longShiftWorkingPattern = WorkingPattern::with(['items'])->where('time', 'siang')->where('division', 'aerplus')->first();


            if (isset($todayLastClockIn)) {
                $todayLastClockInWorkingPatternTime = $todayLastClockIn->workingPattern->time ?? null;
                $todayLastClockInWorkingPatternDivision = $todayLastClockIn->workingPattern->division ?? null;
                if ($todayLastClockInWorkingPatternTime == 'pagi' && $todayLastClockInWorkingPatternDivision == 'aerplus') {
                    $todayDayOrder = Carbon::now()->dayOfWeekIso;
                    $todayWorkingPatternItem = collect($todayLastClockIn->workingPattern->items)->where('order', $todayDayOrder)->first();

                    // Long Shift Working Pattern ----------------
                    $todayLongShiftWorkingPatternItem = collect($longShiftWorkingPattern->items)->where('order', $todayDayOrder)->first();
                    // ----------------

                    if (isset($todayWorkingPatternItem)) {
                        // $workingPatternItemClockOut = date('Y-m-d') . ' ' . $todayWorkingPatternItem->clock_out;
                        // $workingPatternItemClockOut = date('Y-m-d 11:00:00');

                        $workingPatternItemClockOut = date('Y-m-d') . ' ' . $todayLongShiftWorkingPatternItem->clock_out;
                        $todayTime = Carbon::now()->toDateTimeString();
                        if ($todayTime > $workingPatternItemClockOut) {
                            $isLongShiftAvailable = true;
                        }
                    }
                    // $isLongShiftAvailable = true;
                }
                // if($todayLastClockOut)
            }

            if (isset($longShiftWorkingPattern) && $isLongShiftAvailable) {
                $longShiftWorkingPatternId = $longShiftWorkingPattern->id;
            }

            $employee->is_long_shift_available = $isLongShiftAvailable;
            $employee->long_shift_working_pattern_id = $longShiftWorkingPatternId;

            $employee->today_clock_in_time = $todayClockInTime;
            $employee->today_clock_out_time = $todayClockOutTime;

            $isAerplusEmployee = false;

            $employeeDivisionId = $employee->office->division_id ?? null;

            $AERPLUS_DIVISION_ID = 12;

            if (isset($employeeDivisionId) && $employeeDivisionId == $AERPLUS_DIVISION_ID) {
                $isAerplusEmployee = true;
            }
            // $isAerplusEmployee = false;
            $employee->working_pattern_name = $employee->activeWorkingPatterns[0]->name ?? "";

            $employee->is_aerplus_employee = $isAerplusEmployee;

            $employee->today_attendance = $employee->attendances[0] ?? null;

            // Superior number
            // Department 11 - Creative & Design : Yenny Natasamudra - 081293339398
            // - HRD: Gabriel Godwin Laut / Marselino Fau - 081283826079 (Gabriel) / 081280400130 (Marsel)
            // Department 4 - Finance & Accounting: Sulistia Ningsih - 085883391889
            // Designation 20 - Logistik: Krisdianto Sutradjaja - 081219692363
            // Department 10 - Operational Aerplus: Haposan Parulian Purba - 081289088031
            // Division 2 - Event Organizer: Siti Jubaida - 085715233303
            $superiorNumber = "Gabriel Godwin Laut / Marselino Fau - 081283826079 (Gabriel) / 081280400130 (Marsel)";
            // $superiorName = "";
            $departmentId = $employee->activeCareer->jobTitle->designation->department_id ?? null;
            $designationId = $employee->activeCareer->jobTitle->designation_id ?? null;
            $employee->department_id = $departmentId;
            $employee->designation_id = $departmentId;

            // Based on company
            if ($employeeDivisionId == 2) {
                $superiorNumber = "Siti Jubaida - 085715233303";
            }

            // Based on department
            if ($departmentId == 4) {
                $superiorNumber = "Sulistia Ningsih - 085883391889";
            } else if ($departmentId == 10) {
                if ($isAerplusEmployee) {
                    $superiorNumber = "Haposan Parulian Purba - 081289088031";
                }
            } else  if ($departmentId == 11) {
                $superiorNumber = "Yenny Natasamudra - 081293339398";
            }

            // Based on designation
            if ($designationId == 20) {
                $superiorNumber = "Krisdianto Sutradjaja - 081219692363";
            }

            $employee->superior_number = $superiorNumber;

            $employeeCompanyId = $employee->office->division->company->id ?? null;
            $employeeJobTitleId = $employee->activeCareer->job_title_id ?? null;

            $isSrcCleaningService = false;
            if ($employeeCompanyId == "4" && $employeeJobTitleId == "193") {
                $isSrcCleaningService = true;
            }

            $isSrcSecurity = false;
            if ($employeeCompanyId == "4" && $employeeJobTitleId == "192") {
                $isSrcSecurity = true;
            }

            $employee->is_src_cleaning_service = $isSrcCleaningService;
            $employee->is_src_security = $isSrcSecurity;

            // 4, 193
            // 4, 192

            $isOvertimeApprover = Employee::where('active', 1)->where('overtime_approver_id', $employee->id)->count() > 0;

            $employee->is_overtime_approver = $isOvertimeApprover;


            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $employee,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to get data',
                'error' => true,
                'code' => 400,
                'errors' => $e->getMessage(),
            ], 400);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display loans from specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function loans($id)
    {
        //
    }

    /**
     * Display attendances from specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attendances(Request $request, $id)
    {
        try {
            $whereClauses = $request->query();

            // return $whereClauses;

            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if (!isset($startDate) || !isset($endDate)) {
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
            }

            $attendancesQuery = Attendance::query()
                ->where('employee_id', $id);

            unset($whereClauses['start_date']);
            unset($whereClauses['end_date']);
            foreach ($whereClauses as $column => $whereClause) {
                $attendancesQuery->where($column, $whereClauses[$column]);
            }

            $attendancesQuery->whereBetween('date', [$startDate, $endDate]);
            $attendances = $attendancesQuery->get();

            return response()->json([
                'message' => 'OK',
                'error' => false,
                'employee_id' => $id,
                'code' => 200,
                'data' => $attendances,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to get data',
                'error' => true,
                'code' => 400,
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    public function activities(Request $request, $id)
    {
        $whereClause = $request->query();
        try {
            $employee = Employee::find($id);

            if ($employee == null) {
                return response()->json([
                    'message' => 'Employee not found',
                    'error' => true,
                    'code' => 400,
                ], 400);
            }

            $activities = Activity::with(['items'])->where('employee_id', $id)->where($whereClause)->get();

            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $activities
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

    public function checkIns(Request $request, $id)
    {
        $whereClause = $request->query();
        try {
            $employee = Employee::find($id);

            if ($employee == null) {
                return response()->json([
                    'message' => 'Employee not found',
                    'error' => true,
                    'code' => 400,
                ], 400);
            }

            $checkIns = CheckIn::where('employee_id', $id)->where($whereClause)->get();

            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $checkIns
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

    public function sickApplications(Request $request, $id)
    {
        $whereClause = $request->query();
        try {
            $employee = Employee::find($id);

            if ($employee == null) {
                return response()->json([
                    'message' => 'Employee not found',
                    'error' => true,
                    'code' => 400,
                ], 400);
            }

            $sickApplications = SickApplication::where('employee_id', $id)->where($whereClause)->get();

            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $sickApplications
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

    public function leaveApplications(Request $request, $id)
    {
        $whereClause = $request->query();
        try {
            $employee = Employee::find($id);

            if ($employee == null) {
                return response()->json([
                    'message' => 'Employee not found',
                    'error' => true,
                    'code' => 400,
                ], 400);
            }

            $leaveApplications = LeaveApplication::with(['category'])->where('employee_id', $id)->where($whereClause)->get();

            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $leaveApplications
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

    public function remainingLeaves($id)
    {
        try {
            $year = date('Y');
            $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', $year)->orderBy('date')->get();
            $leaveQuota = 12;

            $employee = Employee::with(['leaveApplications' => function ($q) {
                $q->whereHas('category', function ($q2) {
                    $q2->where('type', 'annual_leave');
                })->where('approval_status', 'approved');
            }, 'office.division.company'])->findOrFail($id);

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

            // !If the company is retail
            $companyType = $employee->office->division->company->type ?? null;
            if ($companyType == 'retail') {
                $massLeavesCount = 0;
            }

            $takenLeave = $leaveApplicationsCount + $massLeavesCount;
            $leave = [
                'total' => $leaveQuota,
                'taken' => $takenLeave,
            ];

            return response()->json([
                'message' => 'OK',
                'data' => $leave,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function changePassword(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'password' => 'required|string',
            ]);
            $newPassword = $request->password;

            $credential = Credential::query()->where('employee_id', $id)->first();
            $credential->password = Hash::make($newPassword);
            $credential->save();

            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $credential,
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

    public function aerplusBonusSalaryEmployees()
    {
        try {
            $AERPLUS_DIVISION_ID = 12;
            $active = request()->query('active');

            $employeesQuery = Employee::whereHas('office', function ($q) use ($AERPLUS_DIVISION_ID) {
                $q->where('division_id', $AERPLUS_DIVISION_ID);
            })->with(['office', 'activeCareer.jobTitle']);
            // ->where('active', 1)
            // ->get();
            if (isset($active)) {
                $employeesQuery->where('active', $active);
            }

            $employees = $employeesQuery->get();



            return response()->json([
                'message' => 'OK',
                'data' => $employees,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function attendanceSummary($id)
    {
        try {
            $month = request()->query('month') ?? date('Y-m');

            $startDate = date($month . '-01');
            $endDate = date("Y-m-t", strtotime($startDate));
            $employee = Employee::with(['activeCareer', 'attendances' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
            }, 'activeWorkingPatterns.items'])->findOrFail($id);

            $attendances = $employee->attendances;
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            $activeWorkingPattern = null;
            if (count($employee->activeWorkingPatterns) > 0) {
                $activeWorkingPattern = $employee->activeWorkingPatterns[0];
            }
            $newActiveWorkingPattern = collect($activeWorkingPattern)->all();
            $workingPatternItems = [];

            if (isset($newActiveWorkingPattern['items'])) {
                $workingPatternItems = $newActiveWorkingPattern['items'];
            }

            $eventCalendars = EventCalendar::all();

            $statistics = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'cuti' => 0,
                'na' => 0,
            ];

            $datesRange = $this->getDatesFromRange($startDate, $endDate);
            $finalAttendances = collect($datesRange)->map(function ($date, $key) use ($attendances, $eventCalendars, $days, $workingPatternItems, &$statistics) {
                $carbonDate = Carbon::parse($date);
                $dayIndex = $carbonDate->dayOfWeekIso;
                $attendance = collect($attendances)->where('date', $date)->first();
                $events = collect($eventCalendars)->where('date', $date)->values()->all();
                $workingPatternItem = collect($workingPatternItems)->where('order', $dayIndex)->first();
                // $activeWorkingPatternItems =

                $item = [
                    'date' => $date,
                    'iso_date' => $carbonDate->isoFormat('ll'),
                    'day' => $days[$dayIndex - 1],
                    'day_status' => "workday",
                    'attendance' => null,
                    'events' => $events,
                ];

                if ($attendance !== null) {
                    $item['attendance'] = $attendance->status ?? null;
                    if (isset($item['attendance']['status'])) {
                        $status = $item['attendance']['status'];
                        if ($status == 'hadir') {
                            $statistics['hadir'] += 1;
                        } else if ($status == 'sakit') {
                            $statistics['sakit'] += 1;
                        } else if ($status == 'izin') {
                            $statistics['izin'] += 1;
                        } else if ($status == 'cuti') {
                            $statistics['cuti'] += 1;
                        }
                    }
                } else {
                    $statistics['na'] += 1;
                    if (count($events) > 0) {
                        $item['attendance'] = 'holiday';
                    }
                }

                if ($workingPatternItem !== null) {
                    $item['day_status'] = $workingPatternItem['day_status'];
                    if ($workingPatternItem['day_status'] == 'holiday' && $item['attendance'] == null) {
                        $item['attendance'] = 'holiday';
                    }
                }

                return $item;
            })->all();

            return response()->json([
                'data' => $finalAttendances,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function monthlyAttendances($id)
    {
        try {
            $month = request()->query('month') ?? date('Y-m');

            $startDate = date($month . '-01');
            $endDate = date("Y-m-t", strtotime($startDate));
            $employee = Employee::with(['activeCareer', 'attendances' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
            }, 'activeWorkingPatterns.items'])->findOrFail($id);

            $attendances = $employee->attendances;
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            $activeWorkingPattern = null;
            if (count($employee->activeWorkingPatterns) > 0) {
                $activeWorkingPattern = $employee->activeWorkingPatterns[0];
            }
            $newActiveWorkingPattern = collect($activeWorkingPattern)->all();
            $workingPatternItems = [];

            if (isset($newActiveWorkingPattern['items'])) {
                $workingPatternItems = $newActiveWorkingPattern['items'];
            }

            $eventCalendars = EventCalendar::all();

            $statistics = [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'cuti' => 0,
                'na' => 0,
            ];

            $datesRange = $this->getDatesFromRange($startDate, $endDate);
            $finalAttendances = collect($datesRange)->map(function ($date, $key) use ($attendances, $eventCalendars, $days, $workingPatternItems, &$statistics) {
                $carbonDate = Carbon::parse($date);
                $dayIndex = $carbonDate->dayOfWeekIso;
                $attendance = collect($attendances)->where('date', $date)->first();
                $events = collect($eventCalendars)->where('date', $date)->values()->all();
                $workingPatternItem = collect($workingPatternItems)->where('order', $dayIndex)->first();
                // $activeWorkingPatternItems =

                $item = [
                    'date' => $date,
                    'iso_date' => $carbonDate->isoFormat('ll'),
                    'day' => $days[$dayIndex - 1],
                    'day_status' => "workday",
                    'attendance' => null,
                    'attendace_id' => null,
                    'clock_in_time' => null,
                    'clock_out_time' => null,
                    'clock_in_note' => null,
                    'clock_out_note' => null,
                    'time_late' => "0",
                    'events' => $events,
                ];

                if ($attendance !== null) {
                    $item['attendance'] = $attendance->status ?? null;
                    $item['attendance_id'] = $attendance->id ?? null;
                    $item['clock_in_time'] = $attendance->clock_in_time ?? null;
                    $item['clock_out_time'] = $attendance->clock_out_time ?? null;
                    $item['clock_in_note'] = $attendance->clock_in_note ?? null;
                    $item['clock_out_note'] = $attendance->clock_out_note ?? null;
                    $item['time_late'] = $attendance->time_late ?? 0;
                    if (isset($item['attendance'])) {
                        $status = $item['attendance'];
                        if ($status == 'hadir') {
                            $statistics['hadir'] += 1;
                        } else if ($status == 'sakit') {
                            $statistics['sakit'] += 1;
                            $item['attendance_id'] = $attendance->sick_application_id;
                        } else if ($status == 'izin') {
                            $statistics['izin'] += 1;
                        } else if ($status == 'cuti') {
                            $statistics['cuti'] += 1;
                            $item['attendance_id'] = $attendance->leave_application_id;
                        }
                    }
                } else {
                    $statistics['na'] += 1;
                }

                if ($workingPatternItem !== null) {
                    $item['day_status'] = $workingPatternItem['day_status'];

                    if (count($events) > 0) {
                        $item['day_status'] = 'holiday';
                    }

                    // if ($workingPatternItem['day_status'] == 'holiday' && $item['attendance'] == null) {
                    //     $item['attendance'] = 'holiday';
                    // }
                }

                return $item;
            })->all();

            return response()->json([
                'data' => $finalAttendances,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    public function birthdays()
    {
        try {
            // $dateOfBirth = request()->query('date_of_birth');
            $companyId = request()->query('company_id');

            $employees = Employee::whereHas('office.division', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->with(['activeCareer.jobTitle', 'office.division.company'])->whereRaw("DATE_FORMAT(date_of_birth,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d')")->where('active', 1)->simplePaginate(10)->withQueryString();

            return response()->json([
                'message' => 'OK',
                'data' => $employees,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function birthdaysCount()
    {
        try {
            // $dateOfBirth = request()->query('date_of_birth');
            $companyId = request()->query('company_id');

            $employeesCount = Employee::whereHas('office.division', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->with(['activeCareer.jobTitle', 'office.division.company'])->whereRaw("DATE_FORMAT(date_of_birth,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d')")->where('active', 1)->count();

            return response()->json([
                'message' => 'OK',
                'data' => $employeesCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function insights(Request $request, $id)
    {
        try {
            // Reward
            // $employee = 
            $insights = [];

            $salaryStartDayPeriod = 26;
            $salaryStartMonthPeriod = $request->query('month') ?? date('m');
            $salaryStartYearPeriod = $request->query('year') ?? date('Y');

            $carbonDate = Carbon::parse($salaryStartYearPeriod . '-' . $salaryStartMonthPeriod . '-' . $salaryStartDayPeriod);

            $startDate = $carbonDate->subMonth()->toDateString();
            $endDate = $carbonDate->addMonth()->subDay()->toDateString();

            $eventCalendars = EventCalendar::all();

            $startDateAttendancePeriod = $startDate;
            $endDateAttendancePeriod = $endDate;
            if ($startDate == '2023-03-26' && $endDate == '2023-04-25') {
                $endDateAttendancePeriod = '2023-04-15';
            }
            $periodDates = $this->getDatesFromRange($startDateAttendancePeriod, $endDateAttendancePeriod);

            $employee = Employee::with([
                'attendances' => function ($q) use ($startDateAttendancePeriod, $endDateAttendancePeriod) {
                    $q->whereBetween('date', [$startDateAttendancePeriod, $endDateAttendancePeriod])->orderBy('id', 'desc');
                },
                'activeWorkingPatterns.items',
                'leaveApplications' => function ($q) {
                    $q->where('approval_status', 'approved')->where('leave_category_id', 7);
                }
            ])->find($id);

            $isGetPresenceIncentive = false;

            $attendances = $employee->attendances;
            $workingPatterns = $employee->activeWorkingPatterns;
            $salaryComponents = $employee->salaryComponents;

            $salaryComponentTypes = ['gaji_pokok', 'tunjangan'];

            $incomes = [];
            $workDaysLength = 0;

            $activeWorkingPattern = collect($workingPatterns)->first();
            $notPresentDays = [];

            $dailyWageComponent = collect($salaryComponents)->where('salary_type', 'uang_harian')->first();
            $overtimePayComponent = collect($salaryComponents)->where('salary_type', 'lembur')->first();

            // !DEVELOPMENT ONLY
            $employeeWage = [
                'daily' => 0,
                'daily_coefficient' => 1,
                'overtime' => 0,
                'overtime_coefficient' => 1,
            ];

            if ($dailyWageComponent !== null) {
                if (isset($dailyWageComponent['pivot']['amount']) && isset($dailyWageComponent['pivot']['coefficient'])) {
                    $employeeWage['daily'] = $dailyWageComponent['pivot']['amount'];
                    $employeeWage['daily_coefficient'] = $dailyWageComponent['pivot']['coefficient'];
                }
            }

            if ($dailyWageComponent !== null) {
                if (isset($overtimePayComponent['pivot']['amount']) && isset($overtimePayComponent['pivot']['coefficient'])) {
                    $employeeWage['overtime'] = $overtimePayComponent['pivot']['amount'];
                    $employeeWage['overtime_coefficient'] = $overtimePayComponent['pivot']['coefficient'];
                }
            }

            $employeeStartWorkDate = $employee->start_work_date;
            // $employeeStartWorkDate = '2023-01-22';
            if (isset($employeeStartWorkDate)) {
                if ($employeeStartWorkDate >= $startDate) {
                    $periodDates = $this->getDatesFromRange($employeeStartWorkDate, $endDate);
                }
            }

            $presentDaysCount = 0;

            $totalTimeLate = 0;

            $notClockInAttendances = [];
            $notClockOutAttendances = [];

            $noStatusAttendanceDates = [];

            $lateDays = [];

            $periods = collect($periodDates)->each(function ($date) use ($attendances, $activeWorkingPattern, $eventCalendars, &$notPresentDays, &$workingPatternDayStatus, &$presentDaysCount, &$totalTimeLate, &$notClockInAttendances, &$notClockOutAttendances, &$noStatusAttendanceDates, &$lateDays) {
                // Resource
                $newestAttendance = collect($attendances)->where('date', $date)->first();
                $currentEvents = collect($eventCalendars)->filter(function ($event) use ($date) {
                    return $event['date'] == $date && ($event['type'] == 'libur_nasional' || $event['type'] == 'cuti_bersama');
                })->values()->all();

                $eventCalendarsExist = count($currentEvents) > 0 ? true : false;

                $dayOrder = Carbon::parse($date)->dayOfWeekIso;

                $workingPatternItems = [];
                if (isset($activeWorkingPattern['items'])) {
                    $workingPatternItems = $activeWorkingPattern['items'];
                }
                $workingPatternDay = collect($workingPatternItems)->where('order', $dayOrder)->first();


                if ($newestAttendance == null) {
                    $isHoliday = false;

                    if ($workingPatternDay !== null) {
                        // When status is holiday
                        if ($workingPatternDay['day_status'] == 'holiday') {
                            $isHoliday = true;
                        }
                    }

                    if (!$eventCalendarsExist && !$isHoliday) {
                        array_push($notPresentDays, [
                            'date' => $date,
                            'description' => 'Tidak hadir (tanpa status)',
                            'type' => 'no_status',
                        ]);

                        $noStatusAttendanceDates[] = $date;

                        $notClockInAttendances[] = [
                            'date' => $date,
                        ];

                        $notClockOutAttendances[] = [
                            'date' => $date,
                        ];
                    }

                    if ($eventCalendarsExist) {
                        $presentDaysCount += 1;
                    }
                } else {
                    if ($newestAttendance['status'] == 'hadir') {

                        $timeLate = 0;
                        // $timeLate = (int) $newestAttendance['time_late'];

                        if ($workingPatternDay != null) {
                            // When status is holiday
                            if ($workingPatternDay['day_status'] == 'workday' && !$eventCalendarsExist) {
                                $timeLate = (int) $newestAttendance['time_late'];

                                if ($timeLate > 0) {
                                    $lateDays[] = [
                                        'date' => $date,
                                    ];
                                }
                            }

                            if ($workingPatternDay['day_status'] == 'workday') {
                                $presentDaysCount += 1;
                            }
                        } else {
                            $timeLate = (int) $newestAttendance['time_late'];

                            if ($eventCalendarsExist) {
                                $timeLate = 0;
                            }
                        }

                        $totalTimeLate += $timeLate;

                        if ($newestAttendance['clock_in_time'] == null) {
                            $notClockInAttendances[] = [
                                'date' => $date,
                            ];
                        }

                        if ($newestAttendance['clock_out_time'] == null) {
                            $notClockOutAttendances[] = [
                                'date' => $date,
                            ];
                        }
                    } else {
                        array_push($notPresentDays, [
                            'date' => $date,
                            'description' => 'Tidak hadir (' . $newestAttendance['status'] . ')',
                            'type' => 'time_off',
                        ]);
                    }
                }
            });

            // REWARD
            // Presence Incentive (Insentif Kehadiran)
            if (count($notPresentDays) < 1 && $activeWorkingPattern !== null) {
                $isGetPresenceIncentive = true;
            }

            if ($employee->type == "staff") {
                if (count($lateDays) > 0) {
                    $isGetPresenceIncentive = false;
                }
            }

            $presenceIncentiveAmount = 0;

            if ($isGetPresenceIncentive && ($employee->start_work_date < $startDate)) {
                if ($employee->type == "non_staff") {
                    $presenceIncentiveAmount = $employeeWage['daily'] * 2;
                } else if ($employee->type == "staff") {
                    $presenceIncentiveAmount = 250000;
                }

                $insights[] = [
                    'title' => 'Pertahanin Terus!',
                    'description' => 'Periode ini kamu berpotensi dapet reward kehadiran',
                    'content' => 'Rp ' . number_format($presenceIncentiveAmount, 0, ',', '.'),
                    'sub_content' => '',
                    'type' => 'success',
                ];
            }
            // End: Presence Incentive (Insentif Kehadiran)
            // END: REWARD

            // Calculate new employee salaries

            // PUNISHMENT
            // If Employee Staff
            if ($employee->type == "staff") {
                if ($totalTimeLate > 0) {
                    $latePenaltyAmount = 0;
                    if ($totalTimeLate > 0 && $totalTimeLate <= 60) {
                        $latePenaltyAmount = 50000;
                    } else if ($totalTimeLate >= 61 && $totalTimeLate <= 120) {
                        $latePenaltyAmount = 100000;
                    } else if ($totalTimeLate >= 121 && $totalTimeLate <= 180) {
                        $latePenaltyAmount = 150000;
                    } else if ($totalTimeLate >= 181 && $totalTimeLate <= 240) {
                        $latePenaltyAmount = 200000;
                    } else if ($totalTimeLate >= 241) {
                        $latePenaltyAmount = 250000;
                    }

                    if ($totalTimeLate >= 300) {
                        $previousPeriodSalary = collect($employee->salaries)->first();
                        $previousPeriodSalaryItems = $previousPeriodSalary->items ?? [];
                        $lateSalaryItem = collect($previousPeriodSalaryItems)->where('salary_type', 'late')->first();
                        $lateSalaryItemAmount = $lateSalaryItem->amount ?? 0;
                        $maxLateTimes = floor($lateSalaryItemAmount / 250000) + 1;
                        $latePenaltyAmount = 250000 * $maxLateTimes;
                    }

                    $insights[] = [
                        'title' => 'Ayo Lebih Rajin Lagi!',
                        'description' => 'Periode ini kamu berpotensi dapet denda telat',
                        'content' => 'Rp ' . number_format($latePenaltyAmount, 0, ',', '.'),
                        'sub_content' => number_format($totalTimeLate, 0, ',', '.') . ' Menit',
                        'type' => 'danger',
                    ];
                }
            }
            // END: PUNISHMENT

            // OFF EVENT QUOTA
            $offEventLeaveApplicationDates = LeaveApplication::whereRelation('category', 'type', 'off_event')->where('employee_id', $id)->where('approval_status', 'approved')
                ->get()
                ->filter(function ($offEventLeaveApplication) {
                    $applicationDates = explode(',', $offEventLeaveApplication->application_dates);
                    $validApplicationDates = collect($applicationDates)->filter(function ($applicationDate) {
                        return $applicationDate >= Carbon::now()->toDateString();
                    })->all();

                    $offEventLeaveApplication->valid_application_dates = $validApplicationDates;

                    return count($validApplicationDates) > 0;
                })->flatMap(function ($offEventLeaveApplication) {
                    return $offEventLeaveApplication->valid_application_dates;
                })->all();

            if (count($offEventLeaveApplicationDates) > 0) {
                $insights[] = [
                    'title' => 'Kamu Ada Jatah Cuti Nih!',
                    'description' => 'Kamu punya jatah cuti di tanggal ini',
                    'content' => collect($offEventLeaveApplicationDates)->map(function ($offEventLeaveApplicationDate) {
                        return Carbon::parse($offEventLeaveApplicationDate)->isoFormat('LL');
                    })->join(', '),
                    'sub_content' => 'Off Event',
                    'type' => 'success',
                ];
            }

            return response()->json([
                'message' => 'OK',
                'data' => $insights,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }
}
