<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Office;
use App\Models\OutletOpening;
use App\Models\WorkScheduleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyOutletReportController extends Controller
{
    /**
     * Display daily outlet opening report.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $employeeId = $request->query('employee_id');
            $date = $request->query('date', Carbon::now()->format('Y-m-d'));

            // If no employee_id provided, get current authenticated user's employee
            if (empty($employeeId)) {
                $user = auth()->user();
                if ($user && $user->employee) {
                    $employeeId = $user->employee->id;
                } else {
                    return view('daily-outlet-reports.index', [
                        'outlets' => [],
                        'statistics' => [
                            'total' => 0,
                            'opened' => 0,
                            'not_opened' => 0,
                        ],
                        'date' => $date,
                        'employee' => null,
                    ]);
                }
            }

            // Get employee and their credential
            $employee = Employee::with(['credential'])->find($employeeId);

            if (!$employee) {
                return view('daily-outlet-reports.index', [
                    'outlets' => [],
                    'statistics' => [
                        'total' => 0,
                        'opened' => 0,
                        'not_opened' => 0,
                    ],
                    'date' => $date,
                    'employee' => null,
                ]);
            }

            if (!$employee->credential) {
                return view('daily-outlet-reports.index', [
                    'outlets' => [],
                    'statistics' => [
                        'total' => 0,
                        'opened' => 0,
                        'not_opened' => 0,
                    ],
                    'date' => $date,
                    'employee' => $employee,
                ]);
            }

            // Get accessible offices from credential
            $accessibleOfficesIds = json_decode($employee->credential->accessible_offices ?? "[]", true);

            if (empty($accessibleOfficesIds) || !is_array($accessibleOfficesIds)) {
                return view('daily-outlet-reports.index', [
                    'outlets' => [],
                    'statistics' => [
                        'total' => 0,
                        'opened' => 0,
                        'not_opened' => 0,
                    ],
                    'date' => $date,
                    'employee' => $employee,
                ]);
            }

            // Get total offices count for statistics
            $totalOffices = Office::whereIn('id', $accessibleOfficesIds)->count();

            // Get all offices (no pagination)
            $offices = Office::whereIn('id', $accessibleOfficesIds)->get();
            $officeIds = $offices->pluck('id')->all();

            // Get outlet openings for the date (all offices)
            $outletOpenings = OutletOpening::where('date', $date)
                ->whereIn('office_id', $officeIds)
                ->get()
                ->keyBy('office_id');

            // Get work schedule items for the date (all offices)
            $workScheduleItems = WorkScheduleItem::with(['employee' => function ($q) {
                $q->where('active', 1);
            }])
                ->where('date', $date)
                ->whereIn('office_id', $officeIds)
                ->where(function ($q) {
                    $q->where('is_off', 0)->orWhereNull('is_off');
                })
                ->whereHas('employee', function ($q) {
                    $q->where('active', 1);
                })
                ->get()
                ->groupBy('office_id');

            // Get attendance count per office (employees with status = "hadir") - all offices
            $attendanceCounts = Attendance::where('date', $date)
                ->where('status', 'hadir')
                ->whereHas('employee', function ($q) use ($officeIds) {
                    $q->whereIn('office_id', $officeIds)
                        ->where('active', 1);
                })
                ->join('employees', 'attendances.employee_id', '=', 'employees.id')
                ->selectRaw('employees.office_id, COUNT(*) as count')
                ->groupBy('employees.office_id')
                ->pluck('count', 'office_id');

            // Get all attendances for employees
            $employeeIds = $workScheduleItems->flatten()
                ->pluck('employee_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $attendances = collect();
            if (!empty($employeeIds)) {
                $attendances = Attendance::where('date', $date)
                    ->whereIn('employee_id', $employeeIds)
                    ->get()
                    ->keyBy('employee_id');
            }

            // Build result
            $outlets = $offices->map(function ($office) use ($outletOpenings, $workScheduleItems, $attendanceCounts, $attendances, $date) {
                $officeId = $office->id;
                $outletOpening = $outletOpenings->get($officeId);

                // Get employees from work schedule for this office
                $officeWorkScheduleItems = $workScheduleItems->get($officeId, collect());
                $employees = $officeWorkScheduleItems
                    ->filter(function ($item) {
                        return $item->employee && $item->employee->active == 1;
                    })
                    ->map(function ($item) use ($attendances) {
                        $attendance = $attendances->get($item->employee_id);
                        $attendanceStatus = null;

                        if ($attendance) {
                            $attendanceStatus = $attendance->status ?? 'Tanpa Keterangan';
                        }

                        return [
                            'id' => $item->employee->id,
                            'name' => $item->employee->name,
                            'attendance_status' => $attendanceStatus,
                        ];
                    })
                    ->unique('id')
                    ->values()
                    ->all();

                // Get all employees with credentials that have this office in accessible_offices
                $allEmployees = Employee::with(['credential', 'activeCareer'])
                    ->whereHas('credential', function ($q) use ($officeId) {
                        $q->whereNotNull('accessible_offices');
                    })
                    ->where('active', 1)
                    ->get();

                // Filter employees whose accessible_offices contains this office ID
                $employeesWithAccess = $allEmployees->filter(function ($emp) use ($officeId) {
                    if (!$emp->credential || !$emp->credential->accessible_offices) {
                        return false;
                    }
                    $accessibleOffices = json_decode($emp->credential->accessible_offices, true);
                    return is_array($accessibleOffices) && in_array($officeId, $accessibleOffices);
                });

                // Get area managers (job_title_id = 5)
                $areaManagers = $employeesWithAccess->filter(function ($emp) {
                    return $emp->activeCareer && $emp->activeCareer->job_title_id == 5;
                })->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->name,
                    ];
                })->values()->all();

                // Get supervisors (job_title_id = 3)
                $supervisors = $employeesWithAccess->filter(function ($emp) {
                    return $emp->activeCareer && $emp->activeCareer->job_title_id == 3;
                })->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->name,
                    ];
                })->values()->all();

                // Get leaders (job_title_id = 1)
                $leaders = $employeesWithAccess->filter(function ($emp) {
                    return $emp->activeCareer && $emp->activeCareer->job_title_id == 1;
                })->map(function ($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->name,
                    ];
                })->values()->all();

                return [
                    'id' => $office->id,
                    'name' => $office->name,
                    'open_time' => $office->opening_time ?? $office->open_time ?? null,
                    'is_opened' => $outletOpening !== null,
                    'actual_open_time' => $outletOpening ? $outletOpening->time : null,
                    'timeliness_status' => $outletOpening ? $outletOpening->timeliness_status : null,
                    'present_employees_count' => $attendanceCounts->get($officeId, 0),
                    'employees' => $employees,
                    'area_managers' => $areaManagers,
                    'supervisors' => $supervisors,
                    'leaders' => $leaders,
                ];
            })->values()->all();

            // Get statistics (from all accessible offices)
            $openedOffices = OutletOpening::where('date', $date)
                ->whereIn('office_id', $accessibleOfficesIds)
                ->selectRaw('COUNT(DISTINCT office_id) as count')
                ->value('count') ?? 0;
            $notOpenedOffices = $totalOffices - $openedOffices;

            return view('daily-outlet-reports.index', [
                'outlets' => collect($outlets),
                'statistics' => [
                    'total' => $totalOffices,
                    'opened' => $openedOffices,
                    'not_opened' => $notOpenedOffices,
                ],
                'date' => $date,
                'employee' => $employee,
            ]);
        } catch (\Throwable $th) {
            return view('daily-outlet-reports.index', [
                'outlets' => [],
                'statistics' => [
                    'total' => 0,
                    'opened' => 0,
                    'not_opened' => 0,
                ],
                'date' => $date ?? Carbon::now()->format('Y-m-d'),
                'employee' => null,
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Display daily outlet opening report detail.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $officeId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $officeId)
    {
        try {
            $employeeId = $request->query('employee_id');
            $date = $request->query('date', Carbon::now()->format('Y-m-d'));

            // If no employee_id provided, get current authenticated user's employee
            if (empty($employeeId)) {
                $user = auth()->user();
                if ($user && $user->employee) {
                    $employeeId = $user->employee->id;
                } else {
                    return redirect()->route('daily-outlet-reports.index')
                        ->with('error', 'Employee not found');
                }
            }

            // Get employee and their credential
            $employee = Employee::with(['credential'])->find($employeeId);

            if (!$employee || !$employee->credential) {
                return redirect()->route('daily-outlet-reports.index')
                    ->with('error', 'Employee credential not found');
            }

            // Get accessible offices from credential
            $accessibleOfficesIds = json_decode($employee->credential->accessible_offices ?? "[]", true);

            if (empty($accessibleOfficesIds) || !is_array($accessibleOfficesIds)) {
                return redirect()->route('daily-outlet-reports.index')
                    ->with('error', 'No accessible offices found');
            }

            // Check if office is accessible
            if (!in_array($officeId, $accessibleOfficesIds)) {
                return redirect()->route('daily-outlet-reports.index')
                    ->with('error', 'Office not accessible');
            }

            // Get office
            $office = Office::find($officeId);

            if (!$office) {
                return redirect()->route('daily-outlet-reports.index')
                    ->with('error', 'Office not found');
            }

            // Get outlet opening for the date
            $outletOpening = OutletOpening::where('date', $date)
                ->where('office_id', $officeId)
                ->first();

            // Get work schedule items for the date
            $workScheduleItems = WorkScheduleItem::with(['employee' => function ($q) {
                $q->where('active', 1);
            }])
                ->where('date', $date)
                ->where('office_id', $officeId)
                ->where(function ($q) {
                    $q->where('is_off', 0)->orWhereNull('is_off');
                })
                ->whereHas('employee', function ($q) {
                    $q->where('active', 1);
                })
                ->get();

            // Get employee IDs from work schedule
            $employeeIds = $workScheduleItems
                ->pluck('employee_id')
                ->filter()
                ->unique()
                ->all();

            // Get attendances for these employees
            $attendances = Attendance::where('date', $date)
                ->whereIn('employee_id', $employeeIds)
                ->get()
                ->keyBy('employee_id');

            // Get employees from work schedule for this office
            $employees = $workScheduleItems
                ->filter(function ($item) {
                    return $item->employee && $item->employee->active == 1;
                })
                ->map(function ($item) use ($attendances) {
                    $attendance = $attendances->get($item->employee_id);
                    $attendanceStatus = null;
                    $clockInTime = null;

                    if ($attendance) {
                        $attendanceStatus = $attendance->status ?? 'Tanpa Keterangan';
                        $clockInTime = $attendance->clock_in_time ?? null;
                    }

                    return [
                        'id' => $item->employee->id,
                        'name' => $item->employee->name,
                        'photo' => $item->employee->photo ?? null,
                        'attendance_status' => $attendanceStatus,
                        'clock_in_time' => $clockInTime,
                    ];
                })
                ->unique('id')
                ->values()
                ->all();

            // Get attendance count for this office (employees with status = "hadir")
            $presentEmployeesCount = Attendance::where('date', $date)
                ->where('status', 'hadir')
                ->whereHas('employee', function ($q) use ($officeId) {
                    $q->where('office_id', $officeId)
                        ->where('active', 1);
                })
                ->join('employees', 'attendances.employee_id', '=', 'employees.id')
                ->where('employees.office_id', $officeId)
                ->count();

            // Get all employees with credentials that have this office in accessible_offices
            $allEmployees = Employee::with(['credential', 'activeCareer'])
                ->whereHas('credential', function ($q) use ($officeId) {
                    $q->whereNotNull('accessible_offices');
                })
                ->where('active', 1)
                ->get();

            // Filter employees whose accessible_offices contains this office ID
            $employeesWithAccess = $allEmployees->filter(function ($emp) use ($officeId) {
                if (!$emp->credential || !$emp->credential->accessible_offices) {
                    return false;
                }
                $accessibleOffices = json_decode($emp->credential->accessible_offices, true);
                return is_array($accessibleOffices) && in_array($officeId, $accessibleOffices);
            });

            // Get area managers (job_title_id = 5)
            $areaManagers = $employeesWithAccess->filter(function ($emp) {
                return $emp->activeCareer && $emp->activeCareer->job_title_id == 5;
            })->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                ];
            })->values()->all();

            // Get supervisors (job_title_id = 3)
            $supervisors = $employeesWithAccess->filter(function ($emp) {
                return $emp->activeCareer && $emp->activeCareer->job_title_id == 3;
            })->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                ];
            })->values()->all();

            // Get leaders (job_title_id = 1)
            $leaders = $employeesWithAccess->filter(function ($emp) {
                return $emp->activeCareer && $emp->activeCareer->job_title_id == 1;
            })->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                ];
            })->values()->all();

            $outlet = [
                'id' => $office->id,
                'name' => $office->name,
                'open_time' => $office->opening_time ?? $office->open_time ?? null,
                'is_opened' => $outletOpening !== null,
                'actual_open_time' => $outletOpening ? $outletOpening->time : null,
                'timeliness_status' => $outletOpening ? $outletOpening->timeliness_status : null,
                'present_employees_count' => $presentEmployeesCount,
                'employees' => $employees,
                'area_managers' => $areaManagers,
                'supervisors' => $supervisors,
                'leaders' => $leaders,
            ];

            return view('daily-outlet-reports.show', [
                'outlet' => $outlet,
                'date' => $date,
                'employee' => $employee,
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('daily-outlet-reports.index')
                ->with('error', $th->getMessage());
        }
    }
}
