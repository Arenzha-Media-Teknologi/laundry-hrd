<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Division;
use App\Models\Office;
use App\Models\Employee;
use App\Models\WorkingPattern;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return 'asdasd';
        $date = request()->query('date') ?? date('Y-m-d');
        $companyId = request()->query('company_id');
        $divisionId = request()->query('division_id');
        $officeId = request()->query('office_id');
        $status = request()->query('status');

        $employeesWithAttendancesQuery = Employee::with(['attendances' => function ($q) use ($date) {
            $q->with(['leaveApplication.category', 'longShiftConfirmer', 'permissionCategory'])->where('date', $date)->orderBy('id', 'DESC');
        }, 'activeCareer.jobTitle.designation', 'office' => function ($q) {
            $q->with(['division' => function ($q2) {
                $q2->with(['company']);
            }]);
        }]);

        $filteredCompanyName = ["Semua Perusahaan"];
        $filteredDivisionName = ["Semua Divisi"];
        $filteredOfficeName = ["Semua Kantor"];
        $filteredStatusName = "Semua Status";

        if (isset($companyId) && !empty($companyId)) {
            $companyIds = explode(',', $companyId);
            $employeesWithAttendancesQuery->whereHas('office.division.company', function ($q) use ($companyIds) {
                $q->whereIn('id', $companyIds);
            });
            $filteredCompanyName = Company::query()->whereIn('id', $companyIds)->get()->pluck('name')->all() ?? ["Semua Perusahaan"];
        }

        if (isset($divisionId) && !empty($divisionId)) {
            $divisionIds = explode(',', $divisionId);
            $employeesWithAttendancesQuery->whereHas('office.division', function ($q) use ($divisionIds) {
                $q->whereIn('id', $divisionIds);
            });
            $filteredDivisionName = Division::query()->whereIn('id', $divisionIds)->get()->pluck('name')->all() ?? ["Semua Divisi"];
        }

        if (isset($officeId) && !empty($officeId)) {
            $officeIds = explode(',', $officeId);
            $employeesWithAttendancesQuery->whereHas('office', function ($q) use ($officeIds) {
                $q->whereIn('id', $officeIds);
            });
            $filteredOfficeName = Office::query()->whereIn('id', $officeIds)->get()->pluck('name')->all() ?? ["Semua Kantor"];
        }

        if (isset($status) && !empty($status)) {
            $employeesWithAttendancesQuery->where('active', $status);
            $filteredStatusName = $status == '1' ? 'Aktif' : 'Nonaktif';
        }

        $employeesWithAttendances = $employeesWithAttendancesQuery->get();

        $statistics = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'cuti' => 0,
            'na' => 0,
        ];

        collect($employeesWithAttendances)->each(function ($employee) use (&$statistics) {
            $attendance = collect($employee->attendances)->first();
            if ($attendance !== null) {
                $item['attendance'] = $attendance;
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
            }
        });

        // return $statistics;

        $attendanceDayOrder = Carbon::now()->dayOfWeek;

        $workingPatterns = WorkingPattern::with(['items' => function ($q) use ($attendanceDayOrder) {
            $q->where('order', $attendanceDayOrder);
        }])->get();

        $companies = Company::all();
        $divisions = Division::with(['company'])->get();
        $offices = Office::with(['division.company'])->get();

        return view('attendances.index', [
            'date' => $date,
            'employees' => $employeesWithAttendances,
            'working_patterns' => $workingPatterns,
            'statistics' => $statistics,
            'companies' => $companies,
            'divisions' => $divisions,
            'offices' => $offices,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'office_id' => $officeId,
                'status' => $status,
            ],
            'filtered_company_name' => $filteredCompanyName,
            'filtered_division_name' => $filteredDivisionName,
            'filtered_office_name' => $filteredOfficeName,
            'filtered_status_name' => $filteredStatusName,
        ]);
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
            // Validate request payload
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                // 'working_pattern_id' => 'required|integer',
                // 'clock_in_time' => 'required',
                // 'clock_in_at' => 'required',
                // 'clock_out_time' => 'required',
                // 'clock_out_at' => 'required',
                // 'overtime' => 'required|integer',
                // 'time_late' => 'required|integer',
                // 'status' => 'required',
            ]);

            // Assign request payload to new variable
            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            // $workingPatternId = $request->working_pattern_id;
            $clockInTime = $request->clock_in_time;
            $clockInAt = $request->clock_in_at;
            $clockOutTime = $request->clock_out_time;
            $clockOutAt = $request->clock_out_at;
            $overtime = $request->overtime;
            $timeLate = $request->time_late;
            $createdBy = Auth::user()->employee->id ?? null;

            // Check employee existence 
            $employee = Employee::with(['activeWorkingPatterns' => function ($q) {
                $q->orderBy('created_at', 'DESC');
            }])->find($employeeId);

            $workingPatternId = $request->activeWorkingPatterns[0] ?? null;

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            // Get active working pattern
            $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

            // if ($activeWorkingPattern == null) {
            //     throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
            // }

            // // Get day order of active working pattern
            // $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
            // $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

            // $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
            //     return $item->order == $dayOrder;
            // })->values()->all();

            // if (count($workingPatternDays) < 1) {
            //     throw new Exception('"working pattern day" tidak ditemukan');
            // }

            // // Assign day order 
            // $workingPatternDay = $workingPatternDays[0];

            // // Insert attendane to database
            // $attendance = new Attendance();
            // $attendance->employee_id = $employeeId;
            // $attendance->date = $attendanceDate;
            // // Clock in
            // $attendance->clock_in_time = $clockInTime;
            // $attendance->clock_in_at = $clockInAt;
            // $attendance->clock_in_working_pattern_time = $workingPatternDay->clock_in;
            // // --
            // // Clock out
            // $attendance->clock_out_time = $clockOutTime;
            // $attendance->clock_out_at = $clockOutAt;
            // $attendance->clock_out_working_pattern_time = $workingPatternDay->clock_out;
            // // --
            // $attendance->status = 'hadir';
            // $attendance->time_late = $timeLate;
            // $attendance->overtime = $overtime;
            // $attendance->working_pattern_id = $workingPatternId;
            // $attendance->save();

            // Version 2.0
            $clockInWorkingPatternTime = null;
            $clockOutWorkingPatternTime = null;
            if (isset($activeWorkingPattern)) {

                // Get day order of active working pattern
                $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                    return $item->order == $dayOrder;
                })->values()->all();

                if (count($workingPatternDays) < 1) {
                    throw new Exception('"working pattern day" tidak ditemukan');
                }

                // Assign day order 
                $workingPatternDay = $workingPatternDays[0];

                $clockInWorkingPatternTime = $workingPatternDay->clock_in;
                $clockOutWorkingPatternTime = $workingPatternDay->clock_out;
            }

            // Insert attendane to database
            $attendance = new Attendance();
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            // Clock in
            $attendance->clock_in_time = $clockInTime;
            $attendance->clock_in_at = $clockInAt;
            $attendance->clock_in_working_pattern_time = $clockInWorkingPatternTime;
            // --
            // Clock out
            $attendance->clock_out_time = $clockOutTime;
            $attendance->clock_out_at = $clockOutAt;
            $attendance->clock_out_working_pattern_time = $clockOutWorkingPatternTime;
            // --
            $attendance->status = 'hadir';
            $attendance->time_late = $timeLate;
            $attendance->overtime = $overtime;
            $attendance->working_pattern_id = $workingPatternId;
            $attendance->created_by = $createdBy;
            $attendance->save();

            $employeesWithAttendances = Employee::with(['attendances' => function ($q) use ($attendanceDate) {
                $q->where('date', $attendanceDate)->orderBy('id', 'DESC');
            }])->get();

            return response()->json([
                'message' => 'success',
                // 'data' => [
                //     'today_order' => $attendanceDayOrder,
                //     'day_order' => $dayOrder,
                //     'working_pattern_day' => $workingPatternDay,
                //     'time_late' => $timeLate,
                // ],
                'data' => $attendance,
                'employees' => $employeesWithAttendances,
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
        try {
            // Validate request payload
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                // 'working_pattern_id' => 'required|integer',
                // 'clock_in_time' => 'required',
                // 'clock_in_at' => 'required',
                // 'clock_out_time' => 'required',
                // 'clock_out_at' => 'required',
                // 'overtime' => 'required|integer',
                // 'time_late' => 'required|integer',
                // 'status' => 'required',
            ]);

            // Assign request payload to new variable
            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            $workingPatternId = $request->working_pattern_id;
            $clockInTime = $request->clock_in_time;
            $clockInAt = $request->clock_in_at;
            $clockOutTime = $request->clock_out_time;
            $clockOutAt = $request->clock_out_at;
            $overtime = $request->overtime;
            $timeLate = $request->time_late;
            $updatedBy = Auth::user()->employee->id ?? null;

            // Check employee existence 
            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            // Get active working pattern
            $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

            // if ($activeWorkingPattern == null) {
            //     throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
            // }

            // // Get day order of active working pattern
            // $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
            // $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

            // $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
            //     return $item->order == $dayOrder;
            // })->values()->all();

            // if (count($workingPatternDays) < 1) {
            //     throw new Exception('"working pattern day" tidak ditemukan');
            // }

            // // Assign day order 
            // $workingPatternDay = $workingPatternDays[0];

            // // Insert attendane to database
            // $attendance = Attendance::find($id);
            // $attendance->employee_id = $employeeId;
            // $attendance->date = $attendanceDate;
            // // Clock in
            // $attendance->clock_in_time = $clockInTime;
            // $attendance->clock_in_at = $clockInAt;
            // $attendance->clock_in_working_pattern_time = $workingPatternDay->clock_in;
            // // --
            // // Clock out
            // $attendance->clock_out_time = $clockOutTime;
            // $attendance->clock_out_at = $clockOutAt;
            // $attendance->clock_out_working_pattern_time = $workingPatternDay->clock_out;
            // // --
            // $attendance->status = 'hadir';
            // $attendance->time_late = $timeLate;
            // $attendance->overtime = $overtime;
            // $attendance->working_pattern_id = $workingPatternId;
            // $attendance->save();

            // Version 2.0
            $clockInWorkingPatternTime = null;
            $clockOutWorkingPatternTime = null;
            if (isset($activeWorkingPattern)) {

                // Get day order of active working pattern
                $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                    return $item->order == $dayOrder;
                })->values()->all();

                if (count($workingPatternDays) < 1) {
                    throw new Exception('"working pattern day" tidak ditemukan');
                }

                // Assign day order 
                $workingPatternDay = $workingPatternDays[0];

                $clockInWorkingPatternTime = $workingPatternDay->clock_in;
                $clockOutWorkingPatternTime = $workingPatternDay->clock_out;
            }

            // Insert attendane to database
            $attendance = Attendance::find($id);
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            // Clock in
            if ($clockInTime != null && $clockInTime != "null") {
                $attendance->clock_in_time = $clockInTime;
                $attendance->clock_in_at = $clockInAt;
            }
            $attendance->clock_in_working_pattern_time = $clockInWorkingPatternTime;
            // --
            // Clock out
            if ($clockOutTime != null && $clockOutTime != "null") {
                $attendance->clock_out_time = $clockOutTime;
                $attendance->clock_out_at = $clockOutAt;
            }
            $attendance->clock_out_working_pattern_time = $clockOutWorkingPatternTime;
            // --
            $attendance->status = 'hadir';
            $attendance->time_late = $timeLate;
            $attendance->overtime = $overtime;
            $attendance->working_pattern_id = $workingPatternId;
            $attendance->updated_by = $updatedBy;
            $attendance->save();

            $employeesWithAttendances = Employee::with(['attendances' => function ($q) use ($attendanceDate) {
                $q->where('date', $attendanceDate);
            }])->get();

            return response()->json([
                'message' => 'success',
                // 'data' => [
                //     'today_order' => $attendanceDayOrder,
                //     'day_order' => $dayOrder,
                //     'working_pattern_day' => $workingPatternDay,
                //     'time_late' => $timeLate,
                // ],
                'data' => $attendance,
                'employees' => $employeesWithAttendances,
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
            $company = Attendance::findOrFail($id);
            $company->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    public function upload()
    {
        return view('attendances.upload');
    }

    public function doUploadFromMachineApp2(Request $request)
    {
        if ($request->hasFile('file')) {
            try {
                // $importData = Excel::toCollection(collect([]), $request->file('file'));
                $rejectedData = [];
                $rowIndex = 0;
                $importData = Excel::toCollection(collect([]), $request->file('file'), null, \Maatwebsite\Excel\Excel::CSV);

                // foreach($importData as $data)
                $finalImportData = collect($importData[0])->lazy()->map(function ($data) {
                    $data = str_replace(' ', '', $data[0]);

                    $splittedData = preg_split('/\t/', $data);

                    $userId = (int) str_replace('"', '', $splittedData[1]);
                    $dateTime = $splittedData[3];

                    // $date = date("Y-d-m", strtotime(substr($dateTime, 0, 10)));
                    $date = '';
                    if ($userId !== 0) {
                        $date = Carbon::createFromFormat('d/m/Y', substr($dateTime, 0, 10))->toDateString();
                    }

                    $clock = substr($dateTime, 10);

                    return [
                        'user_id' => $userId,
                        'date' => $date,
                        'clock' => $clock,
                    ];
                    // return $splittedData;
                })->all();

                $finalImportDataDates = collect($finalImportData)->pluck('date');

                $importDataMinDate = min($finalImportDataDates);
                $importDataMaxDate = max($finalImportDataDates);

                $rangeAttendances = Attendance::query()->whereBetween('date', [$importDataMinDate, $importDataMaxDate]);

                $allEmployees = Employee::with(['activeWorkingPatterns.items'])->get();
                $workingPatterns = WorkingPattern::with(['items'])->get();

                // return response()->json([
                //     'data' => $finalImportData,
                // ]);

                $tes = [];

                foreach ($finalImportData as $data) {

                    $employee_id = $data['user_id'];
                    $date = $data['date'];
                    $clock = $data['date'] . ' ' . $data['clock'];
                    $clockOnly = $data['clock'];

                    $data['newdata'] = $employee_id;

                    // $employeeExist = Employee::find($employee_id);
                    $employeeExist = collect($allEmployees)->where('id', $employee_id)->first();

                    if (!isset($employeeExist)) {
                        continue;
                    }

                    // date, employee_id, clock

                    // $attendance = new Attendance;
                    $attendance = [];
                    $attendancesInsertData = [];

                    // 20 Hours Range
                    $HOURS_RANGE = 20 * 60 * 60;

                    // Match date with clock
                    $newClock = $date . ' ' . date_format(date_create($clock), "H:i:s");

                    // return response()->json([
                    //     'data' => $newClock,
                    // ]);

                    // Substract clock with clock range
                    $backClock = strtotime($newClock) - $HOURS_RANGE;

                    $formattedBackClock = date("Y-m-d H:i:s", $backClock);

                    // $backCheckIn = Attendance::query()
                    //     ->where('employee_id', $employee_id)
                    //     ->whereBetween('clock_in', [$formattedBackClock, $clock])
                    //     ->get();
                    $backCheckIn = collect($rangeAttendances)
                        ->where('employee_id', $employee_id)
                        ->whereBetween('clock_in_at', [$formattedBackClock, $clock])
                        ->all();

                    // $backCheckOut = Attendance::query()
                    //     ->where('employee_id', $employee_id)
                    //     ->whereBetween('clock_out', [$formattedBackClock, $clock])
                    //     ->get();
                    $backCheckOut = collect($rangeAttendances)
                        ->where('employee_id', $employee_id)
                        ->whereBetween('clock_out_at', [$formattedBackClock, $clock])
                        ->all();

                    $newestAttendance = collect($backCheckIn)->merge($backCheckOut)->each(function ($attendance) {
                        $attendance['global_clock'] = null;
                        // if ($attendance['type'] == 'check in') {
                        //     $attendance['global_clock'] = $attendance->clock_in;
                        // } else {
                        //     $attendance['global_clock'] = $attendance->clock_out;
                        // }
                        if (isset($attendance['clock_in_time'])) {
                            $attendance['global_clock'] = $attendance['clock_in_at'];
                            $attendance['type'] = $attendance['clock_in'];
                        }

                        if (isset($attendance['clock_out_time'])) {
                            $attendance['global_clock'] = $attendance['clock_out_at'];
                            $attendance['type'] = $attendance['clock_out'];
                        }
                    })->sortByDesc('global_clock')->first();

                    if ($newestAttendance == null) {
                        $morningBottomRange = date('H:i:s', strtotime('06:00:00'));
                        $morningUpperRange = date('H:i:s', strtotime('08:00:00'));

                        // $nightBottomRange = date('H:i:s', strtotime('06:00:00'));
                        // $nightUpperRange = date('H:i:s', strtotime('08:00:00'));

                        $nightBottomRange = date('19:00:00');
                        $nightUpperRange = date('20:00:00');

                        $newCheckClock = $clock;

                        $checkHour = date('H:i:s', strtotime($clock));

                        if ($checkHour > $morningBottomRange && $checkHour <= $morningUpperRange) {
                            $newCheckClock = date('Y-m-d 08:00:00', strtotime($clock));
                        } else if ($checkHour > $nightBottomRange && $checkHour <= $nightUpperRange) {
                            $newCheckClock = date('Y-m-d 20:00:00', strtotime($clock));
                        }

                        try {
                            $attendance['employee_id'] = $employee_id;
                            $attendance['date'] = $date;
                            $attendance['clock_in_time'] = date('H:i:s', strtotime($date));
                            $attendance['clock_in_time_at'] = $date;
                            $attendance['status'] = 'hadir';
                            // $attendance['working_pattern_id'] = 'hadir';
                            $attendance['created_at'] = Carbon::now()->toDateTimeString();
                            $attendance['updated_at'] = Carbon::now()->toDateTimeString();
                            // $attendance['clock_in'] = $newCheckClock;
                            // $attendance['type'] = "check in";
                            // $attendance['category'] = 'present';
                            // $attendance['status'] = 'approved';
                            // $attendance->save();

                            array_push($attendancesInsertData, $attendance);

                            continue;
                        } catch (Exception $e) {
                            continue;
                        }
                    }

                    $intervalLimit = 60 * 4;

                    $checkDiff = Carbon::parse($newestAttendance['global_clock'])->diffInMinutes($clock);
                    if ($checkDiff < $intervalLimit) {
                        continue;
                    }

                    if ($newestAttendance['type'] == 'clock_in') {

                        // $employee = Employee::find($employee_id);

                        $workingHours = Carbon::parse($newestAttendance['clock_in_at'])->diffInHours($clock);
                        $workingMinutes = Carbon::parse($newestAttendance['clock_in_at'])->diffInMinutes($clock);
                        $overtime = 0;
                        $workAsOvertime = false;
                        $maxWorkAsOvertime = 0;
                        $dayStatus = '';
                        $diffWorkingMinutes = 0;

                        // -------------------

                        $employeeActiveWorkingPattern = collect($employeeExist->activeWorkingPatterns)->sortByDesc('created_at')->first();
                        $workingPatternId = $employeeActiveWorkingPattern->id ?? null;

                        // $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);
                        $activeWorkingPattern = collect($workingPatterns)->where('id', $workingPatternId)->first();

                        // if (isset($activeWorkingPattern)) {
                        //     throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
                        // }

                        $attendanceDayOrder = Carbon::parse($date)->dayOfWeek;
                        $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                        $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                            return $item->order == $dayOrder;
                        })->values()->all();

                        // if (count($workingPatternDays) < 1) {
                        //     throw new Exception('"working pattern day" tidak ditemukan');
                        // }

                        $workingPatternDay = $workingPatternDays[0];

                        $calendarHolidays = [];
                        $earlyLeaving = 0;
                        $overtime = 0;

                        if ($workingPatternDay->clock_out !== null) {
                            // function below will return negative value
                            // $diffMinutes = Carbon::parse($attendanceDate . ' ' . $clockOutTime)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_out, false);
                            // function below will return positive value
                            $diffMinutes = Carbon::parse($date . ' ' . $workingPatternDay->clock_out)->diffInMinutes($date . ' ' . $clockOnly, false);
                            // if (count($calendarHolidays) < 1) {
                            //     $earlyLeaving = $diffMinutes < 0 ? abs($diffMinutes) : 0;
                            // }
                            $overtime = $diffMinutes > 0 ? $diffMinutes : 0;
                        }


                        // -------------------

                        if ($newestAttendance['status'] == 'hadir') {

                            $intervalLimit = 10;

                            $checkDiff = Carbon::parse($newestAttendance['clock_in_at'])->diffInMinutes($clock);
                            if ($checkDiff < $intervalLimit) {
                                continue;
                            }

                            $checkinDate = date_format(date_create($newestAttendance['clock_in_at']), "Y-m-d");
                            $checkoutDate = date_format(date_create($clock), "Y-m-d");
                            if ($checkoutDate > $checkinDate) {
                                $date = $checkinDate;
                            }

                            try {
                                $attendance['employee_id'] = $employee_id;
                                $attendance['date'] = $date;
                                $attendance['clock_out_at'] = $clock;
                                $attendance['clock_out_time'] = $clockOnly;
                                // $attendance->type = "check out";
                                $attendance['status'] = 'hadir';
                                $attendance['approval_status'] = 'approved';
                                // $attendance->approval_note = 'WORKING HOURS: ' . $workingHours . ', AS OVERTIME: ' . ($workAsOvertime ? '1' : '0') . ' SHIFT:';
                                $attendance['overtime_duration'] = $overtime;
                                // $attendance->save();
                                continue;
                            } catch (Exception $e) {
                                continue;
                            }
                        } else {
                            try {
                                $attendance['employee_id'] = $employee_id;
                                $attendance['date'] = $date;
                                $attendance['clock_in_at'] = $clock;
                                $attendance['clock_in_time'] = $clockOnly;
                                // $attendance->type = "check in";
                                $attendance['status'] = 'hadir';
                                $attendance['approval_status'] = 'approved';
                                // $attendance->save();
                                continue;
                            } catch (Exception $e) {
                                continue;
                            }
                        }
                    } else if ($newestAttendance->type == 'check out') {

                        $morningBottomRange = date('H:i:s', strtotime('06:00:00'));
                        $morningUpperRange = date('H:i:s', strtotime('08:00:00'));

                        $nightBottomRange = date('H:i:s', strtotime('18:00:00'));
                        $nightUpperRange = date('H:i:s', strtotime('20:00:00'));

                        // $nightBottomRange = date('19:00:00');
                        // $nightUpperRange = date('20:00:00');

                        $newCheckClock = $clock;

                        $checkHour = date('H:i:s', strtotime($clock));

                        if ($checkHour > $morningBottomRange && $checkHour <= $morningUpperRange) {
                            $newCheckClock = date('Y-m-d 08:00:00', strtotime($clock));
                        } else if ($checkHour > $nightBottomRange && $checkHour <= $nightUpperRange) {
                            $newCheckClock = date('Y-m-d 20:00:00', strtotime($clock));
                        }


                        try {
                            // $attendance->employee_id = $employee_id;
                            // $attendance->date = $date;
                            // $attendance->clock_in = $newCheckClock;
                            // $attendance->type = "check in";
                            // $attendance->category = 'present';
                            // $attendance->status = 'approved';
                            // $attendance->save();
                            continue;
                        } catch (Exception $e) {
                            continue;
                        }
                    } else {
                        continue;
                    }

                    continue;
                }

                return response()->json([
                    'message' => 'file received',
                    'error' => false,
                    'code' => 200,
                    'data' => [
                        // 'imported_data' => $importData,
                        // 'final_imported_data' => $finalImportData,
                        'tes' => $tes,
                        'message' => 'OK',
                    ],
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Error while uploading',
                    'error' => true,
                    'code' => 500,
                    'tes' => $tes,
                    'errors' => $e,
                ], 500);
            }
        }

        return response()->json([
            'message' => 'no file was sent',
            'error' => true,
            'code' => 500,
            // 'errors' => $e,
        ], 500);
    }

    public function doUploadFromMachineApp(Request $request)
    {
        if ($request->hasFile('file')) {
            try {
                // $importData = Excel::toCollection(collect([]), $request->file('file'));
                $rejectedData = [];
                $rowIndex = 0;
                $importData = Excel::toCollection(collect([]), $request->file('file'), null, \Maatwebsite\Excel\Excel::CSV);

                // foreach($importData as $data)
                $finalImportData = $importData[0]->map(function ($data) {
                    $data = str_replace(' ', '', $data[0]);

                    $splittedData = preg_split('/\t/', $data);

                    $userId = (int) str_replace('"', '', $splittedData[1]);
                    $dateTime = $splittedData[3];

                    // $date = date("Y-d-m", strtotime(substr($dateTime, 0, 10)));
                    $date = '';
                    if ($userId !== 0) {
                        $date = Carbon::createFromFormat('d/m/Y', substr($dateTime, 0, 10))->toDateString();
                    }

                    $clock = substr($dateTime, 10);

                    return [
                        'user_id' => $userId,
                        'date' => $date,
                        'clock' => $clock,
                    ];
                    // return $splittedData;
                })->all();

                // return response()->json([
                //     'data' => $finalImportData,
                // ]);

                $tes = [];

                foreach ($finalImportData as $data) {

                    $employee_id = $data['user_id'];
                    $date = $data['date'];
                    $clock = $data['date'] . ' ' . $data['clock'];

                    $data['newdata'] = $employee_id;

                    $employeeExist = Employee::find($employee_id);

                    if ($employeeExist == null) {
                        continue;
                    }

                    // date, employee_id, clock

                    $attendance = new Attendance;

                    // 20 Hours Range
                    $HOURS_RANGE = 20 * 60 * 60;

                    // Match date with clock
                    $newClock = $date . ' ' . date_format(date_create($clock), "H:i:s");

                    // return response()->json([
                    //     'data' => $newClock,
                    // ]);

                    // Substract clock with clock range
                    $backClock = strtotime($newClock) - $HOURS_RANGE;

                    $formattedBackClock = date("Y-m-d H:i:s", $backClock);

                    $backCheckIn = Attendance::query()
                        ->where('employee_id', $employee_id)
                        ->whereBetween('clock_in', [$formattedBackClock, $clock])
                        // ->where('category', 'present')
                        // ->whereDate('clock_in', '>=', $backClock)
                        // ->whereDate('clock_in', '<=', $clock)
                        ->get();

                    $backCheckOut = Attendance::query()
                        ->where('employee_id', $employee_id)
                        ->whereBetween('clock_out', [$formattedBackClock, $clock])
                        // ->where('category', 'present')
                        // ->where('clock_out', '>=', $backClock)
                        // ->where('clock_out', '<=', $clock)
                        ->get();

                    $newestAttendance = collect($backCheckIn)->merge($backCheckOut)->each(function ($attendance) {
                        $attendance['global_clock'] = null;
                        if ($attendance['type'] == 'check in') {
                            $attendance['global_clock'] = $attendance->clock_in;
                        } else {
                            $attendance['global_clock'] = $attendance->clock_out;
                        }
                    })->sortByDesc('global_clock')->first();

                    if ($newestAttendance == null) {
                        $morningBottomRange = date('H:i:s', strtotime('06:00:00'));
                        $morningUpperRange = date('H:i:s', strtotime('08:00:00'));

                        $nightBottomRange = date('H:i:s', strtotime('06:00:00'));
                        $nightUpperRange = date('H:i:s', strtotime('08:00:00'));

                        // $nightBottomRange = date('19:00:00');
                        // $nightUpperRange = date('20:00:00');

                        $newCheckClock = $clock;

                        $checkHour = date('H:i:s', strtotime($clock));

                        if ($checkHour > $morningBottomRange && $checkHour <= $morningUpperRange) {
                            $newCheckClock = date('Y-m-d 08:00:00', strtotime($clock));
                        } else if ($checkHour > $nightBottomRange && $checkHour <= $nightUpperRange) {
                            $newCheckClock = date('Y-m-d 20:00:00', strtotime($clock));
                        }

                        try {
                            $attendance->employee_id = $employee_id;
                            $attendance->date = $date;
                            $attendance->clock_in = $newCheckClock;
                            $attendance->type = "check in";
                            $attendance->category = 'present';
                            $attendance->status = 'approved';
                            $attendance->save();
                            continue;
                        } catch (Exception $e) {
                            continue;
                        }
                    }

                    $intervalLimit = 60 * 4;

                    $checkDiff = Carbon::parse($newestAttendance->global_clock)->diffInMinutes($clock);
                    if ($checkDiff < $intervalLimit) {
                        continue;
                    }

                    if ($newestAttendance->type == 'check in') {
                        $employee = Employee::find($employee_id);

                        $workingHours = Carbon::parse($newestAttendance->clock_in)->diffInHours($clock);
                        $workingMinutes = Carbon::parse($newestAttendance->clock_in)->diffInMinutes($clock);
                        $overtime = 0;
                        $workAsOvertime = false;
                        $maxWorkAsOvertime = 0;
                        $dayStatus = '';
                        $diffWorkingMinutes = 0;

                        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

                        $employeeShifts = collect($employee->officeShifts)->filter(function ($shift) {
                            return $shift->pivot->is_active == 1;
                        })->values()->all();
                        if (count($employeeShifts) > 0) {
                            // Carbon::parse($employee->office_shifts[0]->)->diffInHours($clockOut);

                            $activeShift = $employeeShifts[0];
                            foreach ($dayNames as $day) {
                                $lowerDay = strtolower($day);
                                if (date('l', strtotime($date)) == $day) {
                                    // $shiftWorkingHours = $employee->officeShifts[0][$lowerDay . '_working_hours'];
                                    // $shiftWorkingMinutes = $shiftWorkingHours * 60;
                                    // $diffWorkingMinutes = $workingMinutes - $shiftWorkingMinutes;
                                    // $x = $diffWorkingMinutes % 30;
                                    // $y = ($diffWorkingMinutes - $x) / 30;
                                    $workAsOvertime = $activeShift[$lowerDay . '_work_as_overtime'] == 1 ? true : false;
                                    $maxWorkAsOvertime = $activeShift[$lowerDay . '_max_overtime'];

                                    // if ($y > 0) {
                                    //     $z = ($diffWorkingMinutes - $x) - 30;
                                    //     $overtime = 1 + floor($z / 60);
                                    // }
                                    // break;
                                    // -------------------
                                    $shiftOutTime = $activeShift[$lowerDay . '_out_time'];
                                    $checkClock = Carbon::parse($clock)->toTimeString();

                                    $diffShiftClock = 0;
                                    if ($checkClock > $shiftOutTime) {
                                        $diffShiftClock = Carbon::parse($checkClock)->diffInMinutes($shiftOutTime);
                                    }
                                    // 90
                                    // array_push($tes, [
                                    //     'shift_out_time' => $shiftOutTime,
                                    //     'clock' => $checkClock,
                                    // ]);
                                    $x = $diffShiftClock % 30; // 0
                                    $y = ($diffShiftClock - $x) / 30; // 3 

                                    if ($y > 0) {
                                        $z = ($diffShiftClock - $x) - 30; // (90 - 0) - 30 = 60
                                        $overtime = 1 + floor($z / 60); // 1 + floor(60 / 60) = 2
                                    }
                                    break;
                                }
                            }
                        }

                        $overtime = ($overtime > 0) ? $overtime : 0;

                        if ($workAsOvertime) {
                            $overtime = $workingHours;
                            if ($overtime > $maxWorkAsOvertime) {
                                $overtime = $maxWorkAsOvertime;
                            }
                        }

                        if ($newestAttendance->category == 'present') {

                            $intervalLimit = 10;

                            $checkDiff = Carbon::parse($newestAttendance->clock_in)->diffInMinutes($clock);
                            if ($checkDiff < $intervalLimit) {
                                continue;
                            }

                            $checkinDate = date_format(date_create($newestAttendance->clock_in), "Y-m-d");
                            $checkoutDate = date_format(date_create($clock), "Y-m-d");
                            if ($checkoutDate > $checkinDate) {
                                $date = $checkinDate;
                            }

                            try {
                                $attendance->employee_id = $employee_id;
                                $attendance->date = $date;
                                $attendance->clock_out = $clock;
                                $attendance->type = "check out";
                                $attendance->category = 'present';
                                $attendance->status = 'approved';
                                // $attendance->approval_note = 'WORKING HOURS: ' . $workingHours . ', AS OVERTIME: ' . ($workAsOvertime ? '1' : '0') . ' SHIFT:';
                                $attendance->overtime_duration = $overtime;
                                $attendance->save();
                                continue;
                            } catch (Exception $e) {
                                continue;
                            }
                        } else {
                            try {
                                $attendance->employee_id = $employee_id;
                                $attendance->date = $date;
                                $attendance->clock_in = $clock;
                                $attendance->type = "check in";
                                $attendance->category = 'present';
                                $attendance->status = 'approved';
                                $attendance->save();
                                continue;
                            } catch (Exception $e) {
                                continue;
                            }
                        }
                    } else if ($newestAttendance->type == 'check out') {

                        $morningBottomRange = date('H:i:s', strtotime('06:00:00'));
                        $morningUpperRange = date('H:i:s', strtotime('08:00:00'));

                        $nightBottomRange = date('H:i:s', strtotime('18:00:00'));
                        $nightUpperRange = date('H:i:s', strtotime('20:00:00'));

                        // $nightBottomRange = date('19:00:00');
                        // $nightUpperRange = date('20:00:00');

                        $newCheckClock = $clock;

                        $checkHour = date('H:i:s', strtotime($clock));

                        if ($checkHour > $morningBottomRange && $checkHour <= $morningUpperRange) {
                            $newCheckClock = date('Y-m-d 08:00:00', strtotime($clock));
                        } else if ($checkHour > $nightBottomRange && $checkHour <= $nightUpperRange) {
                            $newCheckClock = date('Y-m-d 20:00:00', strtotime($clock));
                        }

                        try {
                            $attendance->employee_id = $employee_id;
                            $attendance->date = $date;
                            $attendance->clock_in = $newCheckClock;
                            $attendance->type = "check in";
                            $attendance->category = 'present';
                            $attendance->status = 'approved';
                            $attendance->save();
                            continue;
                        } catch (Exception $e) {
                            continue;
                        }
                    } else {
                        continue;
                    }

                    continue;
                }

                return response()->json([
                    'message' => 'file received',
                    'error' => false,
                    'code' => 200,
                    'data' => [
                        // 'imported_data' => $importData,
                        // 'final_imported_data' => $finalImportData,
                        'tes' => $tes,
                        'message' => 'OK',
                    ],
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => true,
                    'code' => 500,
                    'tes' => $tes,
                    'errors' => $e,
                ], 500);
            }
        }

        return response()->json([
            'message' => 'no file was sent',
            'error' => true,
            'code' => 500,
            // 'errors' => $e,
        ], 500);
    }

    public function doUploadFromMachineApp3(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                // $destinationPath = public_path('/attendances');
                Storage::disk('local')->put($fileName, file_get_contents($file));

                // return response()->json([
                //     'message' => $destinationPath,
                // ]);
                // Storage::disk('local')->put();
                // $request->file('file')->move($destinationPath, $fileName);


                // $employees = Employee::all();

                // $file = $request->file('file');
                $extractedData = Excel::toCollection(collect([]), $file, null, \Maatwebsite\Excel\Excel::CSV);

                $firstSheetExtractedData = $extractedData[0] ?? [];

                $minDateExtractedData = null;
                $maxDateExtractedData = null;
                if (count($firstSheetExtractedData) > 0) {
                    // Get Min Date
                    $firstRow = $firstSheetExtractedData[0];
                    if (isset($firstRow[1])) {
                        $dateTime = $firstRow[1];
                        $minDateExtractedData = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->subDay()->toDateString();
                    }

                    // Get Max Date
                    $lastIndexExtractedData = count($firstSheetExtractedData) - 1;
                    $lastRow = $firstSheetExtractedData[$lastIndexExtractedData];
                    if (isset($lastRow[1])) {
                        $dateTime = $lastRow[1];
                        $maxDateExtractedData = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->toDateString();
                    }
                }

                if (!isset($minDateExtractedData)) {
                    throw new Error('Cannot get min date');
                }

                if (!isset($maxDateExtractedData)) {
                    throw new Error('Cannot get max date');
                }

                // $rangeAttendances = Attendance::query()->whereBetween('date', [$minDateExtractedData, $maxDateExtractedData]);
                $yesterdayAttendances = Attendance::query()->whereBetween('date', [$minDateExtractedData, $maxDateExtractedData])->get();

                $allEmployees = Employee::with(['activeWorkingPatterns.items'])->get();
                // $workingPatterns = WorkingPattern::with(['items'])->get();
                $newAttendances = collect([])->merge($yesterdayAttendances)->all();

                // return response()->json([
                //     'data' => $newAttendances,
                // ]);

                $newDatas = [];

                $testData = [];

                foreach ($firstSheetExtractedData as $row) {
                    if (!isset($row[0])) {
                        continue;
                    }

                    $employeeId = $row[0];

                    if (!isset($row[1])) {
                        continue;
                    }

                    $dateTime = $row[1];

                    $date = null;
                    $clock = null;
                    if ($employeeId !== 0) {
                        $date = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->toDateString();
                        $clock = Carbon::createFromFormat('d/m/Y H:i', $dateTime)->toTimeString();
                    }

                    if (!isset($date) || !isset($clock)) {
                        continue;
                    }

                    $newData = [
                        'employee_id' => $employeeId,
                        'date_clock' => $date . ' ' . $clock,
                        'date' => $date,
                        'clock' => $clock,
                        'desc' => 'not all',
                    ];

                    $employeeExist = collect($allEmployees)->where('id', $newData['employee_id'])->first();

                    $activeWorkingPattern = collect($employeeExist->activeWorkingPatterns)->first();

                    if (!isset($employeeExist)) {
                        $newData['desc'] = 'employee dont exist';
                        array_push($newDatas, $newData);
                        continue;
                    }

                    $attendance = [];
                    $attendancesInsertData = [];

                    // 20 Hours Range
                    $HOURS_RANGE = 20 * 60 * 60;

                    // Substract clock with clock range
                    $backClock = strtotime($newData['date_clock']) - $HOURS_RANGE;

                    $formattedBackClock = date("Y-m-d H:i:s", $backClock);

                    $newestAttendance = collect($newAttendances)->filter(function ($attendance) use ($formattedBackClock, $newData) {
                        // $clockInAt = isset($attendance->clock_in_at) ? $attendance->clock_in_at : $attendance['clock_in_at'];

                        // $clockOutAt = isset($attendance->clock_out_at) ? $attendance->clock_out_at : $attendance['clock_out_at'];
                        $clockInAt = $attendance['clock_in_at'];

                        $clockOutAt = $attendance['clock_out_at'];

                        return ($clockInAt >= $formattedBackClock && $clockInAt <= $newData['date_clock']) || ($clockOutAt >= $formattedBackClock && $clockOutAt <= $newData['date_clock']);
                    })->where('employee_id', $newData['employee_id'])->sortByDesc('created_at')->first();

                    $newestAttendance3 = isset($newestAttendance) ? $newestAttendance : 'NOTHING';
                    // array_push($testData, [
                    //     'date_clock' => $newData['date_clock'],
                    //     'back_cock' => $formattedBackClock,
                    // ]);

                    $isClockInNewestAttendance = isset($newestAttendance['clock_in_time']) && !isset($newestAttendance['clock_out_time']);

                    $isClockOutNewestAttendance = isset($newestAttendance['clock_out_time']);

                    $INTERVAL_LIMIT = 10;

                    $checkDiff = 0;
                    $checkDiffClockIn = 0;
                    $checkDiffClockOut = 0;

                    if ($isClockInNewestAttendance) {
                        $checkDiff = Carbon::parse($newestAttendance['clock_in_at'])->diffInMinutes($newData['date_clock']);
                        if ($checkDiff < $INTERVAL_LIMIT) {
                            $newData['desc'] = 'interval rejection as clock in';
                            array_push($newDatas, $newData);
                            continue;
                        }
                    } else if ($isClockOutNewestAttendance) {
                        $checkDiffClockIn = Carbon::parse($newestAttendance['clock_in_at'])->diffInMinutes($newData['date_clock']);
                        $checkDiffClockOut = Carbon::parse($newestAttendance['clock_out_at'])->diffInMinutes($newData['date_clock']);
                        if ($checkDiffClockIn < $INTERVAL_LIMIT || $checkDiffClockOut < $INTERVAL_LIMIT) {
                            $newData['desc'] = 'interval rejection as clock out';
                            array_push($newDatas, $newData);
                            continue;
                        }
                    }

                    if (!isset($newestAttendance) || $isClockOutNewestAttendance) {

                        $newData['desc'] = 'assigned as clock in or new data';
                        $newData['newest_attendance'] = $newestAttendance3;
                        $newData['check_diff'] = $checkDiff;
                        array_push($newDatas, $newData);

                        $morningBottomRange = date('H:i:s', strtotime('06:00:00'));
                        $morningUpperRange = date('H:i:s', strtotime('08:00:00'));

                        // $nightBottomRange = date('H:i:s', strtotime('06:00:00'));
                        // $nightUpperRange = date('H:i:s', strtotime('08:00:00'));
                        $nightBottomRange = date('H:i:s', strtotime('18:00:00'));
                        $nightUpperRange = date('H:i:s', strtotime('20:00:00'));

                        $newCheckClock = $clock;

                        $checkHour = date('H:i:s', strtotime($clock));

                        if ($checkHour > $morningBottomRange && $checkHour <= $morningUpperRange) {
                            $newCheckClock = date('Y-m-d 08:00:00', strtotime($clock));
                        } else if ($checkHour > $nightBottomRange && $checkHour <= $nightUpperRange) {
                            $newCheckClock = date('Y-m-d 20:00:00', strtotime($clock));
                        }

                        $newAttendances = collect($newAttendances)->push([
                            'id' => Uuid::uuid4()->toString(),
                            'date' => $newData['date'],
                            'clock_in_time' => $newData['clock'],
                            'clock_in_at' => $newData['date_clock'],
                            'clock_out_time' => null,
                            'clock_out_at' => null,
                            'status' => 'hadir',
                            'employee_id' => $newData['employee_id'],
                            'working_pattern_id' => $activeWorkingPattern->id ?? null,
                            'source' => 'new',
                        ])->all();

                        // array_push($newAttendances, [
                        //     'date' => $newData['date'],
                        //     'clock_in_time' => $newData['clock'],
                        //     'clock_in_at' => $newData['date_clock'],
                        //     'clock_out_time' => null,
                        //     'clock_out_at' => null,
                        //     'status' => 'hadir',
                        //     'employee_id' => $newData['employee_id'],
                        //     'working_pattern_id' => $activeWorkingPattern->id ?? null,
                        // ]);

                        continue;
                    }

                    // $intervalLimit = 10 * 60;

                    // $checkDiff = Carbon::parse($newestAttendance->global_clock)->diffInMinutes($clock);
                    // if ($checkDiff < $intervalLimit) {
                    //     continue;
                    // }

                    if ($isClockInNewestAttendance) {
                        $newData['desc'] = 'assigned as clock out';
                        array_push($newDatas, $newData);

                        $overtime = 0;

                        if (isset($activeWorkingPattern)) {
                            $attendanceDayOrder = Carbon::parse($newData['date'])->dayOfWeek;
                            $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                            $workingPatternDay = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                                return $item->order == $dayOrder;
                            })->values()->first();

                            if (isset($workingPatternDay) && isset($workingPatternDay->clock_out)) {
                                $diffMinutes = Carbon::parse($newData['date'] . ' ' . $workingPatternDay->clock_out)->diffInMinutes($newData['date_clock'], false);

                                $x = $diffMinutes % 30; // 0
                                $y = ($diffMinutes - $x) / 30; // 3 

                                if ($y > 0) {
                                    $z = ($diffMinutes - $x) - 30; // (90 - 0) - 30 = 60
                                    $overtime = 1 + floor($z / 60); // 1 + floor(60 / 60) = 2
                                }
                            }
                        }

                        $overtime = ($overtime > 0) ? $overtime : 0;

                        // $newestAttendance['date'] = $newData['date'];
                        // !ASSIGN THIS TO $new_attendances;

                        $newestAttendanceIndex = collect($newAttendances)->search(function ($attendance) use ($newestAttendance) {
                            return $attendance['id'] == $newestAttendance['id'];
                        });

                        if ($newestAttendanceIndex) {
                            $newAttendances[$newestAttendanceIndex]['clock_out_time'] = $newData['clock'];
                            $newAttendances[$newestAttendanceIndex]['clock_out_at'] = $newData['date_clock'];
                            $newAttendances[$newestAttendanceIndex]['overtime'] = $overtime;
                        }
                    }

                    array_push($testData, [
                        'newest_attendance' => $newestAttendance3,
                        'row' => $newData,
                        'is_clock_in_newest_attendance' => $isClockInNewestAttendance,
                        'is_clock_out_newest_attendance' => $isClockOutNewestAttendance,
                        'is_new_newest_attendance' => !$isClockInNewestAttendance && !$isClockOutNewestAttendance,
                        'check_diff' => $checkDiff,
                    ]);
                };

                // [$haveId, $dontHaveId] = collect($newAttendances)->partition(function ($newAttendance) {
                //     return isset($newAttendance['id']);
                // });

                $newAttendances = collect($newAttendances)->map(function ($attendance) {
                    // $source = $attendance['source'] ?? null;
                    // if (isset($source) && $source == 'new') {
                    //     unset($attendance['id']);
                    // }

                    // return $attendance;
                    $source = $attendance['source'] ?? null;

                    $fields = ['approval_status', 'clock_in_at', 'clock_in_attachment', 'clock_in_device_detail', 'clock_in_ip_address', 'clock_in_latitude', 'clock_in_longitude', 'clock_in_note', 'clock_in_office_latitude', 'clock_in_longitude', 'clock_in_time', 'clock_in_working_pattern_time', 'clock_out_at', 'clock_out_attachment', 'clock_out_device_detail', 'clock_out_ip_address', 'clock_out_latitude', 'clock_out_longitude', 'clock_out_note', 'clock_out_office_latitude', 'clock_out_longitude', 'clock_out_time', 'clock_out_working_pattern_time', 'created_at', 'date', 'early_leaving', 'employee_id', 'id', 'is_long_shift', 'leave_application_id', 'long_shift_working_pattern_clock_in_time', 'long_shift_working_pattern_clock_out_time', 'long_shift_working_pattern_id', 'overtime', 'permission_application_id', 'sick_application_id', 'status', 'time_late', 'working_pattern_id', 'updated_at'];

                    $finalAttendance = [];
                    foreach ($fields as $field) {
                        $finalAttendance[$field] = $attendance[$field] ?? null;

                        if ($source == 'new') {
                            if ($field == 'id') {
                                $finalAttendance[$field] = null;
                            }

                            if ($field == 'created_at' || $field == 'updated_at') {
                                $finalAttendance[$field] = Carbon::now()->toDateTimeString();
                            }
                        }

                        if ($field == 'updated_at') {
                            $finalAttendance[$field] = Carbon::now()->toDateTimeString();
                        }
                    }

                    return $finalAttendance;
                })->all();

                $updateFields = ['clock_out_time', 'clock_out_at', 'overtime', 'updated_at'];

                DB::table('attendances')->upsert($newAttendances, ['id'], $updateFields);

                return response()->json([
                    'message' => 'Data kehadiran berhasil diimpor',
                    'data' => [
                        // 'min_date' => $minDateExtractedData,
                        // 'max_date' => $maxDateExtractedData,
                        'new_attendances' => $newAttendances,
                        // 'yesterday_attendances' => $yesterdayAttendances,
                        'test_data' => $testData,
                        'new_data' => $newDatas,
                    ],
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'error_line' => $th->getLine(),
            ], 500);
        }
    }

    public function doUploadFromMachineAppExperiment(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                // $fileName = time() . '_' . $file->getClientOriginalName();
                // $destinationPath = public_path('/attendances');
                // Storage::disk('local')->put($fileName, file_get_contents($file));

                $extractedData = Excel::toCollection(collect([]), $file, null, \Maatwebsite\Excel\Excel::CSV);

                $firstSheetExtractedData = $extractedData[0] ?? [];
                $firstSheetExtractedData = collect($firstSheetExtractedData)->skip(1)->values()->all();

                $firstSheetExtractedDataDates = [];
                collect($firstSheetExtractedData)->each(function ($currentRow) use (&$firstSheetExtractedDataDates) {
                    $DATE_TIME_COLUMN_INDEX = 3;
                    if (isset($currentRow[$DATE_TIME_COLUMN_INDEX])) {
                        $dateTime = $currentRow[$DATE_TIME_COLUMN_INDEX];
                        $newDate = Carbon::createFromFormat('d/m/Y H:i:s', $dateTime)->toDateString();
                        array_push($firstSheetExtractedDataDates, $newDate);
                    }
                });

                if (count($firstSheetExtractedDataDates) < 1) {
                    throw new Error('Error: cannot get the dates array');
                }

                $minDateExtractedData = min($firstSheetExtractedDataDates);
                $maxDateExtractedData = max($firstSheetExtractedDataDates);

                if (!isset($minDateExtractedData)) {
                    throw new Error('Cannot get min date');
                }

                if (!isset($maxDateExtractedData)) {
                    throw new Error('Cannot get max date');
                }

                $minDateExtractedData = Carbon::parse($minDateExtractedData)->subDay()->toDateString();

                // return response()->json([
                //     'data' => $firstSheetExtractedDataDates,
                //     'min_max_dates' => [$minDateExtractedData, $maxDateExtractedData],
                // ]);

                $yesterdayAttendances = Attendance::query()->whereBetween('date', [$minDateExtractedData, $maxDateExtractedData])->get();


                $allEmployees = Employee::with(['activeWorkingPatterns.items'])->get();
                // return response()->json([
                //     'data' => $allEmployees,
                // ]);
                $newAttendances = collect([])->merge($yesterdayAttendances)->all();

                $newDatas = [];

                $testData = [];
                foreach ($firstSheetExtractedData as $row) {
                    // 0 => Departemen: "OUR COMPANY",
                    // 1 => Nama: 165, 
                    // 2 => No.ID: 165,
                    // 3 => Tgl/Waktu: "07\/01\/2023 14:00:48",
                    // 4 => Lokasi ID: 1,
                    // 5 => No.PIN: null,
                    // 6 => Kode Verifikasi: "FACE",
                    // 7 => No. Kartu: null
                    $EMPLOYEE_ID_COLUMN_INDEX = 1;
                    $DATE_TIME_COLUMN_INDEX = 3;

                    if (!isset($row[$EMPLOYEE_ID_COLUMN_INDEX])) {
                        continue;
                    }

                    $employeeId = $row[$EMPLOYEE_ID_COLUMN_INDEX];

                    if (!isset($row[$DATE_TIME_COLUMN_INDEX])) {
                        continue;
                    }

                    $dateTime = $row[$DATE_TIME_COLUMN_INDEX];

                    $date = null;
                    $clock = null;
                    if ($employeeId !== 0) {
                        $date = Carbon::createFromFormat('d/m/Y H:i:s', $dateTime)->toDateString();
                        $clock = Carbon::createFromFormat('d/m/Y H:i:s', $dateTime)->toTimeString();
                    }

                    if (!isset($date) || !isset($clock)) {
                        continue;
                    }

                    $newData = [
                        'employee_id' => $employeeId,
                        'date_clock' => $date . ' ' . $clock,
                        'date' => $date,
                        'clock' => $clock,
                        'desc' => 'not all',
                    ];


                    $employeeExist = collect($allEmployees)->where('id', $newData['employee_id'])->first();

                    // array_push($newDatas, $employeeExist);
                    // continue;

                    if (!isset($employeeExist)) {
                        $newData['desc'] = 'employee dont exist';
                        array_push($newDatas, $newData);
                        continue;
                    }

                    $activeWorkingPattern = collect($employeeExist->activeWorkingPatterns)->first();

                    $attendance = [];
                    $attendancesInsertData = [];

                    // 20 Hours Range
                    $HOURS_RANGE = 20 * 60 * 60;

                    // Substract clock with clock range
                    $backClock = strtotime($newData['date_clock']) - $HOURS_RANGE;

                    $formattedBackClock = date("Y-m-d H:i:s", $backClock);
                    $newData['formatted_back_clock'] = $formattedBackClock;

                    $filteredNewestAttendances = collect($newAttendances)->filter(function ($attendance) use ($formattedBackClock, $newData) {
                        // $clockInAt = isset($attendance->clock_in_at) ? $attendance->clock_in_at : $attendance['clock_in_at'];

                        // $clockOutAt = isset($attendance->clock_out_at) ? $attendance->clock_out_at : $attendance['clock_out_at'];
                        $clockInAt = $attendance['clock_in_at'];

                        $clockOutAt = $attendance['clock_out_at'];

                        return ($clockInAt >= $formattedBackClock && $clockInAt <= $newData['date_clock']) || ($clockOutAt >= $formattedBackClock && $clockOutAt <= $newData['date_clock']);
                    })->where('employee_id', $newData['employee_id'])->sortByDesc('created_at');

                    $filteredNewestAttendances2 = $filteredNewestAttendances->all();
                    $newData['filtered_newest_attendances'] = $filteredNewestAttendances2;

                    $newestAttendance = $filteredNewestAttendances->first();

                    $newestAttendance3 = isset($newestAttendance) ? $newestAttendance : 'NOTHING';
                    // array_push($testData, [
                    //     'date_clock' => $newData['date_clock'],
                    //     'back_cock' => $formattedBackClock,
                    // ]);

                    $isClockInNewestAttendance = isset($newestAttendance['clock_in_time']) && !isset($newestAttendance['clock_out_time']);

                    $isClockOutNewestAttendance = isset($newestAttendance['clock_out_time']);

                    $INTERVAL_LIMIT = 10;

                    $checkDiff = 0;
                    $checkDiffClockIn = 0;
                    $checkDiffClockOut = 0;

                    if ($isClockInNewestAttendance) {
                        $checkDiff = Carbon::parse($newestAttendance['clock_in_at'])->diffInMinutes($newData['date_clock']);
                        if ($checkDiff < $INTERVAL_LIMIT) {
                            $newData['desc'] = 'interval rejection as clock in';
                            array_push($newDatas, $newData);
                            continue;
                        }
                    } else if ($isClockOutNewestAttendance) {
                        $checkDiffClockIn = Carbon::parse($newestAttendance['clock_in_at'])->diffInMinutes($newData['date_clock']);
                        $checkDiffClockOut = Carbon::parse($newestAttendance['clock_out_at'])->diffInMinutes($newData['date_clock']);
                        if ($checkDiffClockIn < $INTERVAL_LIMIT || $checkDiffClockOut < $INTERVAL_LIMIT) {
                            $newData['desc'] = 'interval rejection as clock out';
                            array_push($newDatas, $newData);
                            continue;
                        }
                    }

                    if (!isset($newestAttendance) || $isClockOutNewestAttendance) {

                        $newData['desc'] = 'assigned as clock in or new data';
                        $newData['newest_attendance'] = $newestAttendance3;
                        $newData['check_diff'] = $checkDiff;
                        array_push($newDatas, $newData);

                        $morningBottomRange = date('H:i:s', strtotime('06:00:00'));
                        $morningUpperRange = date('H:i:s', strtotime('08:00:00'));

                        // $nightBottomRange = date('H:i:s', strtotime('06:00:00'));
                        // $nightUpperRange = date('H:i:s', strtotime('08:00:00'));
                        $nightBottomRange = date('H:i:s', strtotime('18:00:00'));
                        $nightUpperRange = date('H:i:s', strtotime('20:00:00'));

                        $newCheckClock = $clock;

                        $checkHour = date('H:i:s', strtotime($clock));

                        if ($checkHour > $morningBottomRange && $checkHour <= $morningUpperRange) {
                            $newCheckClock = date('Y-m-d 08:00:00', strtotime($clock));
                        } else if ($checkHour > $nightBottomRange && $checkHour <= $nightUpperRange) {
                            $newCheckClock = date('Y-m-d 20:00:00', strtotime($clock));
                        }

                        $timeLate = 0;

                        if (isset($activeWorkingPattern)) {
                            $attendanceDayOrder = Carbon::parse($newData['date'])->dayOfWeek;
                            $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                            $workingPatternDay = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                                return $item->order == $dayOrder;
                            })->values()->first();

                            if (isset($workingPatternDay) && isset($workingPatternDay->clock_out)) {
                                $diffMinutes = Carbon::parse($newData['date'] . ' ' . $workingPatternDay->clock_in)->diffInMinutes($newData['date_clock'], false);
                                $timeLate = $diffMinutes;
                            }
                        }

                        $newAttendances = collect($newAttendances)->push([
                            'id' => Uuid::uuid4()->toString(),
                            'date' => $newData['date'],
                            'clock_in_time' => $newData['clock'],
                            'clock_in_at' => $newData['date_clock'],
                            'clock_out_time' => null,
                            'clock_out_at' => null,
                            'status' => 'hadir',
                            'time_late' => $timeLate,
                            'employee_id' => $newData['employee_id'],
                            'working_pattern_id' => $activeWorkingPattern->id ?? null,
                            'source' => 'new',
                            'created_at' => Carbon::now()->toDateTimeString(),
                        ])->all();

                        continue;
                    }

                    if ($isClockInNewestAttendance) {
                        $newData['desc'] = 'assigned as clock out';
                        array_push($newDatas, $newData);

                        $overtime = 0;

                        if (isset($activeWorkingPattern)) {
                            $attendanceDayOrder = Carbon::parse($newData['date'])->dayOfWeek;
                            $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                            $workingPatternDay = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                                return $item->order == $dayOrder;
                            })->values()->first();

                            if (isset($workingPatternDay) && isset($workingPatternDay->clock_out)) {
                                $diffMinutes = Carbon::parse($newData['date'] . ' ' . $workingPatternDay->clock_out)->diffInMinutes($newData['date_clock'], false);

                                // ? ROUNDED IN HOURS UNIT
                                // $x = $diffMinutes % 30; // 0
                                // $y = ($diffMinutes - $x) / 30; // 3 

                                // if ($y > 0) {
                                //     $z = ($diffMinutes - $x) - 30; // (90 - 0) - 30 = 60
                                //     $overtime = 1 + floor($z / 60); // 1 + floor(60 / 60) = 2
                                // }

                                // ? RAW AMOUNT
                                $overtime = $diffMinutes;
                            }
                        }

                        $overtime = ($overtime > 0) ? $overtime : 0;

                        // $newestAttendance['date'] = $newData['date'];
                        // !ASSIGN THIS TO $new_attendances;

                        $newestAttendanceIndex = collect($newAttendances)->search(function ($attendance) use ($newestAttendance) {
                            return $attendance['id'] == $newestAttendance['id'];
                        });

                        if ($newestAttendanceIndex) {
                            $newAttendances[$newestAttendanceIndex]['clock_out_time'] = $newData['clock'];
                            $newAttendances[$newestAttendanceIndex]['clock_out_at'] = $newData['date_clock'];
                            $newAttendances[$newestAttendanceIndex]['overtime'] = $overtime;
                            $newAttendances[$newestAttendanceIndex]['created_at'] = Carbon::now()->toDateTimeString();
                        }
                    }

                    array_push($testData, [
                        'newest_attendance' => $newestAttendance3,
                        'row' => $newData,
                        'is_clock_in_newest_attendance' => $isClockInNewestAttendance,
                        'is_clock_out_newest_attendance' => $isClockOutNewestAttendance,
                        'is_new_newest_attendance' => !$isClockInNewestAttendance && !$isClockOutNewestAttendance,
                        'check_diff' => $checkDiff,
                    ]);
                };

                // return response()->json([
                // 'data' => collect($testData)->filter(function ($t) {
                //     return $t['row']['employee_id'] == 54;
                // }),
                //     'data' => collect($newDatas)->where('employee_id', 58)->all(),
                // ]);

                $newAttendances = collect($newAttendances)->map(function ($attendance) {
                    // $source = $attendance['source'] ?? null;
                    // if (isset($source) && $source == 'new') {
                    //     unset($attendance['id']);
                    // }

                    // return $attendance;
                    $source = $attendance['source'] ?? null;

                    $fields = ['approval_status', 'clock_in_at', 'clock_in_attachment', 'clock_in_device_detail', 'clock_in_ip_address', 'clock_in_latitude', 'clock_in_longitude', 'clock_in_note', 'clock_in_office_latitude', 'clock_in_longitude', 'clock_in_time', 'clock_in_working_pattern_time', 'clock_out_at', 'clock_out_attachment', 'clock_out_device_detail', 'clock_out_ip_address', 'clock_out_latitude', 'clock_out_longitude', 'clock_out_note', 'clock_out_office_latitude', 'clock_out_longitude', 'clock_out_time', 'clock_out_working_pattern_time', 'created_at', 'date', 'early_leaving', 'employee_id', 'id', 'is_long_shift', 'leave_application_id', 'long_shift_working_pattern_clock_in_time', 'long_shift_working_pattern_clock_out_time', 'long_shift_working_pattern_id', 'overtime', 'permission_application_id', 'sick_application_id', 'status', 'time_late', 'working_pattern_id', 'updated_at'];

                    $finalAttendance = [];
                    foreach ($fields as $field) {
                        $finalAttendance[$field] = $attendance[$field] ?? null;

                        if ($source == 'new') {
                            if ($field == 'id') {
                                $finalAttendance[$field] = null;
                            }

                            if ($field == 'created_at' || $field == 'updated_at') {
                                $finalAttendance[$field] = Carbon::now()->toDateTimeString();
                            }
                        }

                        if ($field == 'updated_at') {
                            $finalAttendance[$field] = Carbon::now()->toDateTimeString();
                        }
                    }

                    return $finalAttendance;
                })->all();

                // return response()->json([
                //     'data' => $newAttendances,
                // ]);

                $updateFields = ['clock_out_time', 'clock_out_at', 'overtime', 'updated_at'];

                foreach (collect($newAttendances)->chunk(100) as $chunk) {
                    DB::table('attendances')->upsert($chunk->toArray(), ['id'], $updateFields);
                }

                return response()->json([
                    'message' => 'Data kehadiran berhasil diimpor',
                    'data' => [
                        // 'min_date' => $minDateExtractedData,
                        // 'max_date' => $maxDateExtractedData,
                        'new_attendances' => $newAttendances,
                        // 'yesterday_attendances' => $yesterdayAttendances,
                        'test_data' => $testData,
                        'new_data' => $newDatas,
                    ],
                ]);
                // !------------------------
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'error_line' => $th->getLine(),
            ], 500);
        }
    }
}
