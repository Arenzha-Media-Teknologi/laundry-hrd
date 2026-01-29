<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\LeaveApplication;
use App\Models\Office;
use App\Models\PermissionApplication;
use App\Models\SickApplication;
use App\Models\WarningLetter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventCalendars = EventCalendar::query()->where('date', date('Y-m-d'))->get();

        $currentDate = date('Y-m-d');
        $sevenDaysBackDate = Carbon::now()->subWeek()->toDateString();

        $needActions = [];

        // return $needActions;

        $sickApplications = SickApplication::with(['employee'])->whereBetween('date', [$sevenDaysBackDate, $currentDate])->where('approval_status', 'pending')->get()->map(function ($application) {
            $application->type = 'sakit';
            return $application;
        })->all();
        $permissionApplications = PermissionApplication::with(['employee'])->whereBetween('date', [$sevenDaysBackDate, $currentDate])->where('approval_status', 'pending')->get()->map(function ($application) {
            $application->type = 'izin';
            return $application;
        })->all();
        // ->where('approval_status', 'pending')
        $leaveApplications = LeaveApplication::with(['employee', 'category'])->whereBetween('date', [$sevenDaysBackDate, $currentDate])->where('approval_status', 'pending')->get()->map(function ($application) {
            $application->type = 'cuti';
            return $application;
        })->all();

        $allTimeOffs = collect($sickApplications)->merge($permissionApplications)->merge($leaveApplications)->sortByDesc('date');
        $takeCount = 5;
        $allTimeOffsCount = $allTimeOffs->count();
        $remainingTimeOffsCount = $allTimeOffsCount - $takeCount;
        $timeOffs = $allTimeOffs->take($takeCount)->all();

        // return $timeOffs;

        // ATTENDANCE
        $date = request()->query('date') ?? date('Y-m-d');
        $companyId = request()->query('company_id');
        $divisionId = request()->query('division_id');
        $officeId = request()->query('office_id');
        $status = request()->query('status');
        $isFilterActive = false;

        $employeesWithAttendancesQuery = Employee::with(['attendances' => function ($q) use ($date) {
            $q->with(['leaveApplication.category'])->where('date', $date)->orderBy('id', 'DESC');
        }, 'activeCareer.jobTitle.designation', 'office' => function ($q) {
            $q->with(['division' => function ($q2) {
                $q2->with(['company']);
            }]);
        }]);

        if (isset($companyId) && !empty($companyId)) {
            $isFilterActive = true;
            $employeesWithAttendancesQuery->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        if (isset($divisionId) && !empty($divisionId)) {
            $isFilterActive = true;
            $employeesWithAttendancesQuery->whereHas('office.division', function ($q) use ($divisionId) {
                $q->where('id', $divisionId);
            });
        }

        if (isset($officeId) && !empty($officeId)) {
            $isFilterActive = true;
            $employeesWithAttendancesQuery->whereHas('office', function ($q) use ($officeId) {
                $q->where('id', $officeId);
            });
        }

        // if (isset($status) && !empty($status)) {
        //     $employeesWithAttendancesQuery->where('active', $status);
        // }

        $employeesWithAttendances = $employeesWithAttendancesQuery->where('active', 1)->get();

        if (isset($status) && !empty($status)) {
            $isFilterActive = true;
            $employeesWithAttendances = $employeesWithAttendances->filter(function ($employee) use ($status) {
                $attendanceStatus = $employee->attendances[0]->status ?? 'na';
                return $attendanceStatus == $status;
            })->all();
        }

        // COUNT
        $statistic = [
            'sakit' => 0,
            'cuti' => 0,
            'na' => 0,
        ];

        collect($employeesWithAttendances)->each(function ($employee) use (&$statistic) {
            $attendanceStatus = $employee->attendances[0]->status ?? 'na';
            if ($attendanceStatus == "sakit") {
                $statistic['sakit'] += 1;
            } else if ($attendanceStatus == "cuti") {
                $statistic['cuti'] += 1;
            } else if ($attendanceStatus == "na") {
                $statistic['na'] += 1;
            }
        });

        // --- TODO
        $lateAttendanceTodos = [];
        $outsideAttendanceTodos = [];
        $runningActivityTodos = [];
        // $lateAttendanceTodos = $this->getLateAttendanceTodos();
        // $outsideAttendanceTodos = $this->getOutsideAttendanceTodos();
        // $runningActivityTodos = $this->getRunningActivityTodos();

        // FILTER
        $companies = Company::all();
        $divisions = Division::all();
        $offices = Office::all();

        // Get late employees data
        $lateEmployeesData = $this->getLateEmployeesData();

        return view('dashboard.index', [
            'timeoffs' => $timeOffs,
            'remaining_timeoffs_count' => $remainingTimeOffsCount,
            'event_calendars' => $eventCalendars,
            'employees_with_attendances' => $employeesWithAttendances,
            'companies' => $companies,
            'divisions' => $divisions,
            'offices' => $offices,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'office_id' => $officeId,
                'status' => $status,
            ],
            'statistic' => $statistic,
            'is_filter_active' => $isFilterActive,
            'need_actions' => $needActions,
            'late_attendance_todos' => $lateAttendanceTodos,
            'outside_attendance_todos' => $outsideAttendanceTodos,
            'running_activity_todos' => $runningActivityTodos,
            'late_employees_data' => $lateEmployeesData,
        ]);
    }

    /**
     * Get late employees data for current month
     * - Employees with 3 late attendances
     * - Employees with 2 late attendances in SP1 period
     * - Employees with 1 late attendance in SP2 period
     */
    private function getLateEmployeesData()
    {
        $currentDate = Carbon::now();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Get active SP1 and SP2 for employees (based on effective_start_date and effective_end_date)
        $activeSP1 = WarningLetter::where('type', 'sp1')
            ->where('effective_start_date', '<=', $currentDate->toDateString())
            ->where('effective_end_date', '>=', $currentDate->toDateString())
            ->pluck('employee_id')
            ->toArray();

        $activeSP2 = WarningLetter::where('type', 'sp2')
            ->where('effective_start_date', '<=', $currentDate->toDateString())
            ->where('effective_end_date', '>=', $currentDate->toDateString())
            ->pluck('employee_id')
            ->toArray();

        // Helper function to group attendances by employee
        $groupAttendancesByEmployee = function ($attendances) {
            $employeeLateCounts = [];
            foreach ($attendances as $attendance) {
                // Skip if employee is null (deleted employee)
                if (!$attendance->employee) {
                    continue;
                }

                $employeeId = $attendance->employee_id;
                if (!isset($employeeLateCounts[$employeeId])) {
                    $employeeLateCounts[$employeeId] = [
                        'employee' => $attendance->employee,
                        'count' => 0,
                        'dates' => []
                    ];
                }
                $employeeLateCounts[$employeeId]['count']++;
                $employeeLateCounts[$employeeId]['dates'][] = $attendance->date;
            }
            return $employeeLateCounts;
        };

        // Tab SP1: Absensi dengan warning_letter_created_at = null, warning_letter_type = null, telat >= 3, tidak ada SP aktif
        $lateAttendancesForSP1 = Attendance::where('time_late', '>', 0)
            ->where('status', 'hadir')
            ->whereNull('warning_letter_created_at')
            ->whereNull('warning_letter_type')
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->with(['employee' => function ($q) {
                $q->with(['office' => function ($q2) {
                    $q2->with(['division' => function ($q3) {
                        $q3->with(['company']);
                    }]);
                }]);
            }])
            ->get();

        $employeeLateCountsForSP1 = $groupAttendancesByEmployee($lateAttendancesForSP1);
        $threeTimesLate = [];
        foreach ($employeeLateCountsForSP1 as $employeeId => $data) {
            // Only show if count >= 3 and no active SP1 or SP2
            if ($data['count'] >= 3 && !in_array($employeeId, $activeSP1) && !in_array($employeeId, $activeSP2)) {
                $threeTimesLate[] = [
                    'employee' => $data['employee'],
                    'count' => $data['count'],
                    'dates' => $data['dates']
                ];
            }
        }

        // Tab SP2: Absensi dengan warning_letter_created_at != null, warning_letter_type = 'sp1', telat >= 2, ada SP1 aktif
        $lateAttendancesForSP2 = Attendance::where('time_late', '>', 0)
            ->where('status', 'hadir')
            ->whereNotNull('warning_letter_created_at')
            ->where('warning_letter_type', 'sp2')
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->with(['employee' => function ($q) {
                $q->with(['office' => function ($q2) {
                    $q2->with(['division' => function ($q3) {
                        $q3->with(['company']);
                    }]);
                }]);
            }])
            ->get();

        $employeeLateCountsForSP2 = $groupAttendancesByEmployee($lateAttendancesForSP2);
        $twoTimesLateInSP1 = [];
        foreach ($employeeLateCountsForSP2 as $employeeId => $data) {
            // Only show if count >= 2 and has active SP1
            if ($data['count'] >= 2 && in_array($employeeId, $activeSP1)) {
                $twoTimesLateInSP1[] = [
                    'employee' => $data['employee'],
                    'count' => $data['count'],
                    'dates' => $data['dates']
                ];
            }
        }

        // Tab SP3: Absensi dengan warning_letter_created_at != null, warning_letter_type = 'sp2', telat >= 1, ada SP2 aktif
        $lateAttendancesForSP3 = Attendance::where('time_late', '>', 0)
            ->where('status', 'hadir')
            ->whereNotNull('warning_letter_created_at')
            ->where('warning_letter_type', 'sp3')
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->with(['employee' => function ($q) {
                $q->with(['office' => function ($q2) {
                    $q2->with(['division' => function ($q3) {
                        $q3->with(['company']);
                    }]);
                }]);
            }])
            ->get();

        $employeeLateCountsForSP3 = $groupAttendancesByEmployee($lateAttendancesForSP3);
        $oneTimeLateInSP2 = [];
        foreach ($employeeLateCountsForSP3 as $employeeId => $data) {
            // Only show if count >= 1 and has active SP2
            if ($data['count'] >= 1 && in_array($employeeId, $activeSP2)) {
                $oneTimeLateInSP2[] = [
                    'employee' => $data['employee'],
                    'count' => $data['count'],
                    'dates' => $data['dates']
                ];
            }
        }

        return [
            'three_times_late' => $threeTimesLate,
            'two_times_late_in_sp1' => $twoTimesLateInSP1,
            'one_time_late_in_sp2' => $oneTimeLateInSP2,
        ];
    }

    public function getLateAttendanceTodos()
    {
        try {
            $currentDate = date('Y-m-d');

            $lateAttendanceTodos = Attendance::whereDoesntHave('issueSettlements', function ($q) {
                $q->where('type', 'late_attendance');
            })->with(['employee' => function ($q) {
                $q->with(['activeCareer.jobTitle.designation', 'office' => function ($q) {
                    $q->with(['division' => function ($q2) {
                        $q2->with(['company']);
                    }]);
                }]);
            }])
                // ->where(function ($query) {
                //     $query->where('clock_in_is_inside_office_radius', 0)
                //         ->orWhere('time_late', '>', 0);
                // })
                ->where('time_late', '>', 0)
                ->where('date', $currentDate)
                ->where('status', 'hadir')
                ->get();
            // $needActions = $needActionAttendances;
            return response()->json([
                'message' => 'OK',
                'data' => $lateAttendanceTodos,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    public function getOutsideAttendanceTodos()
    {
        try {
            $currentDate = date('Y-m-d');

            $lateAttendanceTodos = Attendance::whereDoesntHave('issueSettlements', function ($q) {
                $q->where('type', 'outside_attendance');
            })->with(['employee' => function ($q) {
                $q->with(['activeCareer.jobTitle.designation', 'office' => function ($q) {
                    $q->with(['division' => function ($q2) {
                        $q2->with(['company']);
                    }]);
                }]);
            }])
                // ->where(function ($query) {
                //     $query->where('clock_in_is_inside_office_radius', 0)
                //         ->orWhere('time_late', '>', 0);
                // })
                ->where('clock_in_is_inside_office_radius', 0)
                ->where('date', $currentDate)
                ->get();
            // $needActions = $needActionAttendances;
            return response()->json([
                'message' => 'OK',
                'data' => $lateAttendanceTodos,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    public function getRunningActivityTodos()
    {
        try {
            $currentDate = date('Y-m-d');

            $needActions = [];

            $runningActivityTodos = Activity::whereDoesntHave('issueSettlements', function ($q) {
                $q->where('type', 'running_activity');
            })->with(['employee' => function ($q) {
                $q->with(['activeCareer.jobTitle.designation', 'office' => function ($q) {
                    $q->with(['division' => function ($q2) {
                        $q2->with(['company']);
                    }]);
                }]);
            }])->whereNull('check_out_time')->where('date', '<', $currentDate)->get();
            // return $needActionActivities;


            return response()->json([
                'message' => 'OK',
                'data' => $runningActivityTodos,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Get Todo 
    public function getNeedActions()
    {
        try {
            $currentDate = date('Y-m-d');

            $needActions = [];

            $needActionActivities = Activity::with(['employee'])->whereNull('check_out_time')->where('date', '<', $currentDate)->get();
            // return $needActionActivities;

            $needActionAttendances = Attendance::with(['employee' => function ($q) {
                $q->with(['activeCareer.jobTitle.designation', 'office' => function ($q) {
                    $q->with(['division' => function ($q2) {
                        $q2->with(['company']);
                    }]);
                }]);
            }])
                ->where(function ($query) {
                    $query->where('clock_in_is_inside_office_radius', 0)
                        ->orWhere('time_late', '>', 0);
                })
                ->where('date', $currentDate)
                ->get();
            $needActions = $needActionAttendances;


            return response()->json([
                'message' => 'OK',
                'data' => $needActions,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
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
}
