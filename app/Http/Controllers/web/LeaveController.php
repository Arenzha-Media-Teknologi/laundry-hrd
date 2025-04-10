<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\Leave;
use App\Models\WorkingPattern;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AERPLUS_DIVISION_ID = 12;
        $permissions = auth()->user()->group->permissions ?? "[]";
        $permissions = json_decode($permissions);
        $aerplusOnly = in_array('aerplus_only', $permissions);
        $exceptServer = in_array('except_server', $permissions);

        $year = date('Y');
        $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', $year)->orderBy('date')->get();
        // return $massLeaves;
        // $leaveQuota = 12;
        $employeesWithleave = Employee::query();
        if ($aerplusOnly) {
            $employeesWithleave = $employeesWithleave->whereHas('office', function ($q2) {
                $q2->where('division_id', 12);
            });
        }

        if ($exceptServer) {
            $employeesWithleave = $employeesWithleave->whereHas('activeCareer', function ($q2) {
                $q2->whereIn('job_title_id', [190, 204, 233, 239]);
            });
        }

        $employeesWithleave = $employeesWithleave->with(['leaveApplications' => function ($q) use ($year) {
            $q->whereHas('category', function ($q2) {
                $q2->where('type', 'annual_leave');
            })
                ->where('approval_status', 'approved')
                ->where('application_dates', 'like', '%' . $year . '%');
        }, 'activeCareer.jobTitle', 'leave', 'office.division.company'])
            ->where('active', 1)
            ->get()
            ->lazy()
            ->map(function ($employee) use ($year, $massLeaves) {
                $leaveQuota = $employee->leave->total ?? 0;
                // return ['ok' => 'ok'];
                // Leave remainings
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

                $mappedByMonths = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($startDate, $endDate) {
                    return $date >= $startDate && $date <= $endDate;
                })->values()->groupBy(function ($date) {
                    $month = -1;
                    $explodedDate = explode('-', $date);
                    if (isset($explodedDate[1])) {
                        $month = (int) $explodedDate[1];
                    }
                    return $month;
                })->all();

                $mappedByMonthsCount = [];
                for ($monthIndex = 1; $monthIndex <= 12; $monthIndex++) {
                    $applicationsCount = collect($mappedByMonths)->filter(function ($date, $month) use ($monthIndex) {
                        return $month == $monthIndex;
                    })->flatten()->count();

                    $monthMassLeaveCount = collect($massLeaves)->filter(function ($massLeave) use ($monthIndex) {
                        $month = -1;
                        $explodedDate = explode('-', $massLeave->date);
                        if (isset($explodedDate[1])) {
                            $month = (int) $explodedDate[1];
                        }

                        return $month == $monthIndex;
                    })->count();

                    // !If the company is retail
                    $companyType = $employee->office->division->company->type ?? null;
                    if ($companyType == 'retail') {
                        $monthMassLeaveCount = 0;
                    }

                    $totalTakenLeave = $applicationsCount + $monthMassLeaveCount;

                    array_push($mappedByMonthsCount, $totalTakenLeave < 1 ? '' : $totalTakenLeave);
                    // array_push($mappedByMonthsCount, $applicationsCount);
                }

                $monthLeaveApplications = $mappedByMonthsCount;

                // return $monthLeaveApplications;


                // ---------------
                // $monthLeaveApplications = collect($employee->leaveApplications)->map(function ($application) use ($year) {
                //     // $applicationDates = explode(',', $application->application_dates);
                //     $explodedApplicationDates = explode(',', $application->application_dates);
                //     // $applicationDates = collect($explodedApplicationDates)->filter(function ($date) use ($year) {
                //     //     $applicationYear = -1;
                //     //     $explodedDate = explode('-', $date);
                //     //     if (isset($explodedDate[0])) {
                //     //         $applicationYear = (int) $explodedDate[0];
                //     //     }

                //     //     return $applicationYear == $year;
                //     // })->all();

                //     $mappedByMonths = collect($explodedApplicationDates)->filter(function ($date) use ($year) {
                //         $applicationYear = -1;
                //         $explodedDate = explode('-', $date);
                //         if (isset($explodedDate[0])) {
                //             $applicationYear = (int) $explodedDate[0];
                //         }

                //         return $applicationYear == $year;
                //     })->groupBy(function ($date) {
                //         $month = -1;
                //         $explodedDate = explode('-', $date);
                //         if (isset($explodedDate[1])) {
                //             $month = (int) $explodedDate[1];
                //         }
                //         return $month;
                //     })->all();

                //     $mappedByMonthsCount = [];
                //     for ($monthIndex = 1; $monthIndex <= 12; $monthIndex++) {
                //         $applicationsCount = collect($mappedByMonths)->filter(function ($date, $month) use ($monthIndex) {
                //             return $month == $monthIndex;
                //         })->count();

                //         array_push($mappedByMonthsCount, $applicationsCount < 1 ? '' : $applicationsCount);
                //     }

                //     return $mappedByMonthsCount;
                // })->first();
                // ---------------

                // return $monthLeaveApplications;

                $employee->grouped_leave_applications = $monthLeaveApplications;

                $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                    return explode(',', $leaveApplicationDates);
                })->all();

                $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);
                // $leavePeriod = new CarbonPeriod('2022-01-01', '2022-12-30');
                // $leavePeriodDates = [];
                // foreach ($leavePeriod as $key => $date) {
                //     $leavePeriodDates[] = $date->format('Y-m-d');
                // }
                // $startDate = date('Y-01-01');
                // $endDate = date('Y-12-t');

                // $lengthOfWorking = Carbon::parse($employee->start_work_date)->diffInYears($startDate);
                // if ($lengthOfWorking < 1) {
                //     $leaveQuota = 0;
                // }

                $leaveApplicationsCount = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($startDate, $endDate) {
                    return $date >= $startDate && $date <= $endDate;
                })->count();

                $massLeavesCount = collect($massLeaves)->count();

                $takenLeave = $leaveApplicationsCount + $massLeavesCount;

                if (!isset($employee->leave)) {
                    $takenLeave = 0;
                }

                $leave = [
                    'total' => $leaveQuota,
                    'taken' => $takenLeave,
                ];

                $employee->leave = $leave;

                return $employee;
                // if (isset($monthLeaveApplications[5])) {
                //     return $monthLeaveApplications[5];
                // }
                // return null;
            })->all();

        // return $employeesWithleave;
        // return $employeesWithleave;

        return view('leaves.index', [
            'employees' => $employeesWithleave,
            'mass_leaves' => $massLeaves,
            'working_patterns' => [],
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
        try {
            $total = $request->total;
            $employeeId = $request->employee_id;

            $year = date('Y');
            $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', $year)->orderBy('date')->get();
            $employee = Employee::with(['leave'])->findOrFail($employeeId);
            $leaveQuota = $employee->leave->total ?? 0;

            // Leave remainings
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-t');

            $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                return explode(',', $leaveApplicationDates);
            })->all();

            $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);

            // $lengthOfWorking = Carbon::parse($employee->start_work_date)->diffInYears($startDate);
            // if ($lengthOfWorking < 1) {
            //     $leaveQuota = 0;
            // }

            $leaveApplicationsCount = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($startDate, $endDate) {
                return $date >= $startDate && $date <= $endDate;
            })->count();

            $massLeavesCount = collect($massLeaves)->count();

            $takenLeave = $leaveApplicationsCount + $massLeavesCount;

            if (!isset($employee->leave)) {
                $takenLeave = 0;
            }

            $leave = [
                'total' => $leaveQuota,
                'taken' => $takenLeave,
            ];

            // if ($total < $takenLeave) {
            //     throw new Error('Jatah cuti baru tidak boleh kurang dari jumlah cuti diambil');
            // }

            // return $leave;

            $leave = Leave::updateOrCreate(
                ['employee_id' => $employeeId],
                ['total' => $total, 'taken' => 0, 'employee_id' => $employeeId]
            );
            // $leave = Leave::findOrFail($id);

            // $leave->total = $total;
            // $leave->save();

            return response()->json([
                'message' => 'Perubahan cuti telah tersimpan',
                'data' => $leave,
            ]);
        } catch (\Throwable $e) {
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
        //
    }

    /**
     * Reset leave
     *
     * @return \Illuminate\Http\Response
     */
    public function reset()
    {
        try {
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-t');
            $employees = Employee::all()->map(function ($employee) use ($startDate, $endDate) {
                // Leave remainings
                $leaveQuota = 12;
                $lengthOfWorking = Carbon::parse($employee->start_work_date)->diffInYears($startDate);
                if ($lengthOfWorking < 1) {
                    $leaveQuota = 0;
                }

                return [
                    'total' => $leaveQuota,
                    'taken' => 0,
                    'employee_id' => $employee->id,
                ];
            })->all();

            // return $employees;

            Leave::upsert($employees, ['employee_id'], ['total', 'taken', 'employee_id']);

            return response()->json([
                'message' => 'Perubahan cuti telah tersimpan',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
