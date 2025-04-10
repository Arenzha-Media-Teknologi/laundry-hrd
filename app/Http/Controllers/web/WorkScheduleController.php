<?php

namespace App\Http\Controllers\web;

use App\Exports\workschedules\WorkScheduleByEmployeeExport;
use App\Exports\workschedules\WorkScheduleByOfficeExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DailySalary;
use App\Models\Employee;
use App\Models\Office;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleItem;
use App\Models\WorkScheduleWorkingPattern;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class WorkScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $year = date('Y');

        if ($request->query('year') !== null) {
            $year = $request->query('year');
        }

        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // $schedules = WorkSchedule::select(['id', 'start_date', 'end_date'])
        //     ->whereYear('end_date', $year)
        //     ->get()
        //     ->groupBy([function ($schedule) {
        //         return (int) date('m', strtotime($schedule->end_date)) - 1;
        //     }, function ($schedule) {
        //         return $schedule->start_date . '/' . $schedule->end_date;
        //     }])->sort()
        //     ->all();
        $schedules = WorkSchedule::select(['id', 'start_date', 'end_date'])
            ->whereYear('end_date', $year)
            ->get()->groupBy([function ($schedule) {
                return (int) date('m', strtotime($schedule->end_date)) - 1;
            }])->all();

        // return $schedules;

        return view('work-schedule.index', [
            'schedules' => $schedules,
            'months' => $months,
            'year' => $year,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::all();
        $workScheduleWorkingPatterns = WorkScheduleWorkingPattern::all();
        $usedWorkScheduleDates = WorkSchedule::all()->flatMap(function ($workSchedule) {
            return $this->getDatesFromRange($workSchedule->start_date, $workSchedule->end_date);
        })->all();

        // return $workSchedulesDates;

        $offices = Office::all();
        // return 'asdsd';
        return view('work-schedule.create', [
            'offices' => $offices,
            'companies' => $companies,
            'work_schedule_working_patterns' => $workScheduleWorkingPatterns,
            'used_work_schedule_dates' => $usedWorkScheduleDates,
        ]);
    }

    /**
     * Generate daily salaries for aerplus
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($startDate == null || $endDate == null) {
            return response()->json([
                'message' => 'Tanggal awal dan tanggal akhir diperlukan',
            ], 400);
        }

        try {
            $employees = Employee::with([
                'office',
                'activeCareer.jobTitle',
                'salaryComponents' => function ($q) {
                    $q->orderBy('employee_salary_component.id', 'DESC')->orderBy('employee_salary_component.effective_date', 'DESC');
                },
            ])->where('aerplus_daily_salary', 1)->where('active', 1)->get();

            // return collect($employees[0])->except(['attendances', 'salary_components'])->all();

            $periodDates = $this->getDatesFromRange($startDate, $endDate);

            // Number of days
            $numberOfDays = Carbon::parse($startDate)->diffInDays($endDate);

            $employees = collect($employees)->map(function ($employee) use ($periodDates) {
                $schedules = collect($periodDates)->map(function ($date) {
                    return [
                        'work_schedule_working_pattern_id' => '',
                        'office_id' => '',
                    ];
                })->all();

                $dailyWage = collect($employee->salaryComponents)->where('salary_type', 'uang_harian')->first()['pivot']['amount'] ?? 0;

                $employee->schedules = $schedules;
                $employee->daily_wage = $dailyWage;

                return $employee;
            })->all();

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'employees' => $employees,
                ],
            ]);
            // return view('daily-salaries.index', [
            //     'salaries' => $salaries,
            // ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
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
            $newWorkSchedule = new WorkSchedule();
            $newWorkSchedule->start_date = $request->start_date;
            $newWorkSchedule->end_date = $request->end_date;
            $newWorkSchedule->created_by = Auth::id();
            $newWorkSchedule->save();

            $employees = $request->employees;
            $selectedPeriod = $request->selected_period;

            $newWorkScheduleItemsPayload = [];
            collect($employees)->each(function ($employee) use ($selectedPeriod, $newWorkSchedule, &$newWorkScheduleItemsPayload) {
                collect($selectedPeriod)->each(function ($period, $index) use ($newWorkSchedule, &$newWorkScheduleItemsPayload, $employee) {
                    if (!empty(($employee['schedules'][$index]['work_schedule_working_pattern_id'] ?? null))) {
                        $payload = [
                            'date' => $period['date'],
                            'employee_id' => $employee['id'],
                            'office_id' => $employee['schedules'][$index]['office_id'] ?? null,
                            'work_schedule_id' => $newWorkSchedule->id,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];

                        $workingPatternId = $employee['schedules'][$index]['work_schedule_working_pattern_id'];
                        if ($workingPatternId != 'off') {
                            $payload['work_schedule_working_pattern_id'] = $employee['schedules'][$index]['work_schedule_working_pattern_id'];
                            $payload['is_off'] = 0;
                        } else {
                            $payload['work_schedule_working_pattern_id'] = null;
                            $payload['is_off'] = 1;
                        }

                        $newWorkScheduleItemsPayload[] = $payload;
                    }
                });
            });

            DB::table('work_schedule_items')->insert($newWorkScheduleItemsPayload);

            DB::commit();

            return response()->json([
                'data' => $newWorkScheduleItemsPayload,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
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
        $workSchedule = WorkSchedule::with(['items' => function ($q) {
            $q->with(['employee'])->where('is_off', 0);
        }])->find($id);

        $workScheduleWorkingPatterns = WorkScheduleWorkingPattern::all();

        $offices = Office::all();

        $groupedWorkScheduleItems = collect($workSchedule->items)->groupBy(['office_id', 'work_schedule_working_pattern_id', 'date']);
        // $workSchedule->grouped_schedule_items = $groupedWorkScheduleItems;
        // return $groupedWorkScheduleItems;
        // return $workSchedule->grouped_schedule_items;

        $periodDates = $this->getDatesFromRange($workSchedule->start_date, $workSchedule->end_date);

        return view('work-schedule.detail', [
            'work_schedule' => $workSchedule,
            'grouped_schedule_items' => $groupedWorkScheduleItems,
            'work_schedule_working_patterns' => $workScheduleWorkingPatterns,
            'offices' => $offices,
            'period_dates' => $periodDates,
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
        $workSchedule = WorkSchedule::with(['items.employee'])->findOrFail($id);
        $companies = Company::all();
        $workScheduleWorkingPatterns = WorkScheduleWorkingPattern::all();
        $usedWorkScheduleDates = WorkSchedule::all()->flatMap(function ($workSchedule) {
            return $this->getDatesFromRange($workSchedule->start_date, $workSchedule->end_date);
        })->all();

        $offices = Office::all();

        return view('work-schedule.edit', [
            'work_schedule' => $workSchedule,
            'offices' => $offices,
            'companies' => $companies,
            'work_schedule_working_patterns' => $workScheduleWorkingPatterns,
            'used_work_schedule_dates' => $usedWorkScheduleDates,
        ]);
    }

    /**
     * Generate daily salaries for aerplus
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateEditData(Request $request)
    {
        try {
            $workScheduleId = $request->query('id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if ($workScheduleId == null) {
                return response()->json([
                    'message' => 'ID jadwal diperlukan',
                ], 400);
            }

            if ($startDate == null || $endDate == null) {
                return response()->json([
                    'message' => 'Tanggal awal dan tanggal akhir diperlukan',
                ], 400);
            }

            $workSchedule = WorkSchedule::with(['items' => function ($q) {
                $q->orderBy('id', 'DESC');
            }])->findOrFail($workScheduleId);
            $groupedWorkScheduleItems = collect($workSchedule->items)->groupBy(['employee_id', 'date'])->all();

            // return $groupedWorkScheduleItems;

            $employees = Employee::with([
                'office',
                'activeCareer.jobTitle',
                'salaryComponents' => function ($q) {
                    $q->orderBy('employee_salary_component.id', 'DESC')->orderBy('employee_salary_component.effective_date', 'DESC');
                },
            ])->where('aerplus_daily_salary', 1)->where('active', 1)->get();

            // return collect($employees[0])->except(['attendances', 'salary_components'])->all();

            $periodDates = $this->getDatesFromRange($startDate, $endDate);

            // Number of days
            $numberOfDays = Carbon::parse($startDate)->diffInDays($endDate);

            $employees = collect($employees)->map(function ($employee) use ($periodDates, $groupedWorkScheduleItems) {
                $schedules = collect($periodDates)->map(function ($date) use ($employee, $groupedWorkScheduleItems) {
                    $workingPattern = collect($groupedWorkScheduleItems[$employee->id][$date] ?? [])->last() ?? null;
                    $workingPatternId = $workingPattern->work_schedule_working_pattern_id ?? '';
                    $officeId = $workingPattern->office_id ?? '';
                    if ($workingPattern != null) {
                        if (($workingPattern->is_off ?? null) == 1) {
                            $workingPatternId = 'off';
                        }
                    }
                    return [
                        'work_schedule_working_pattern_id' => $workingPatternId,
                        'office_id' => $officeId,
                    ];
                })->all();

                $dailyWage = collect($employee->salaryComponents)->where('salary_type', 'uang_harian')->first()['pivot']['amount'] ?? 0;

                $employee->schedules = $schedules;
                $employee->daily_wage = $dailyWage;

                return $employee;
            })->all();

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'employees' => $employees,
                ],
            ]);
            // return view('daily-salaries.index', [
            //     'salaries' => $salaries,
            // ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
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
            $workSchedule = WorkSchedule::find($id);

            $employees = $request->employees;
            $selectedPeriod = $request->selected_period;

            $workScheduleItemsPayload = [];
            collect($employees)->each(function ($employee) use ($selectedPeriod, $workSchedule, &$workScheduleItemsPayload) {
                collect($selectedPeriod)->each(function ($period, $index) use ($workSchedule, &$workScheduleItemsPayload, $employee) {
                    if (!empty(($employee['schedules'][$index]['work_schedule_working_pattern_id'] ?? null))) {
                        $payload = [
                            'date' => $period['date'],
                            'employee_id' => $employee['id'],
                            'office_id' => $employee['schedules'][$index]['office_id'] ?? null,
                            'work_schedule_id' => $workSchedule->id,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];

                        $workingPatternId = $employee['schedules'][$index]['work_schedule_working_pattern_id'];
                        if ($workingPatternId != 'off') {
                            $payload['work_schedule_working_pattern_id'] = $employee['schedules'][$index]['work_schedule_working_pattern_id'];
                            $payload['is_off'] = 0;
                        } else {
                            $payload['work_schedule_working_pattern_id'] = null;
                            $payload['is_off'] = 1;
                        }

                        $workScheduleItemsPayload[] = $payload;
                    }
                });
            });

            $workSchedule->items()->delete();
            // DB::table('work_schedule_items')->where('work_schedule_id', $id)->delete();
            DB::table('work_schedule_items')->insert($workScheduleItemsPayload);

            DB::commit();

            return response()->json([
                'data' => $workScheduleItemsPayload,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
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
            $workSchedule = WorkSchedule::find($id);
            $workSchedule->items()->delete();
            $workSchedule->delete();

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get date interval period
     */
    private function getDatesFromRange($start, $end, $format = 'Y-m-d')
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

    /**
     * Export schedule by employee report
     */
    public function exportByEmployee(Request $request)
    {
        try {
            $id = $request->query('id');

            if (empty($id)) {
                throw new Error('params "id" are required');
            }

            $workSchedule = WorkSchedule::with(['items' => function ($q) {
                $q->with(['employee' => function ($q2) {
                    $q2->with(['salaryComponents' => function ($q) {
                        $q->orderBy('employee_salary_component.id', 'DESC')->orderBy('employee_salary_component.effective_date', 'DESC');
                    }, 'activeCareer.jobTitle']);
                }, 'office', 'workScheduleWorkingPattern'])->orderBy('id', 'DESC');
            }])->findOrFail($id);
            $periods = $this->getDatesFromRange($workSchedule->start_date, $workSchedule->end_date);

            $groupedWorkScheduleItems = collect($workSchedule->items)->groupBy(['employee_id', 'date'])->all();

            $employees = Employee::where('active', 1)->orderBy('name', 'ASC')->get()->map(function ($employee) use ($groupedWorkScheduleItems, $periods) {
                $schedules = collect($periods)->map(function ($date) use ($groupedWorkScheduleItems, $employee) {
                    return $groupedWorkScheduleItems[$employee->id][$date][0] ?? null;
                });

                $dailyWage = collect($employee->salaryComponents)->where('salary_type', 'uang_harian')->first()['pivot']['amount'] ?? 0;

                $employee->schedules = $schedules;
                $employee->daily_wage = $dailyWage;

                return $employee;
            })->all();

            // return $employees;
            // return view('work-schedule.export.export-by-employee', [
            //     'employees' => $employees,
            //     'periods' => $periods,
            // ]);
            $startDate = $workSchedule->start_date;
            $endDate = $workSchedule->end_date;

            return Excel::download(new WorkScheduleByEmployeeExport($employees, $periods, $startDate, $endDate), 'REKAPITULASI JADWAL KERJA PERIODE ' . $startDate . ' - ' . $endDate . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Export aerplus report
     */
    public function exportByOffice(Request $request)
    {
        try {
            $id = $request->query('id');

            if (empty($id)) {
                throw new Error('params "id" are required');
            }

            $workSchedule = WorkSchedule::with(['items' => function ($q) {
                $q->with(['employee' => function ($q2) {
                    $q2->with(['activeCareer.jobTitle']);
                }, 'office', 'workScheduleWorkingPattern'])->orderBy('id', 'DESC');
            }])->findOrFail($id);
            $periods = $this->getDatesFromRange($workSchedule->start_date, $workSchedule->end_date);

            $groupedWorkScheduleItems = collect($workSchedule->items)->groupBy(['office_id', 'date', 'work_schedule_working_pattern_id'])->all();

            $workScheduleWorkingPatterns = WorkScheduleWorkingPattern::orderBy('start_time')->orderBy('end_time')->orderBy('id')->get();

            $offices = Office::all()->map(function ($office) use ($periods, $groupedWorkScheduleItems, $workScheduleWorkingPatterns) {
                $periods = collect($periods)->map(function ($date) use ($groupedWorkScheduleItems, $workScheduleWorkingPatterns, $office) {
                    $schedules = collect($workScheduleWorkingPatterns)->map(function ($workScheduleWorkingPattern) use ($groupedWorkScheduleItems, $date, $office) {
                        return $groupedWorkScheduleItems[$office->id][$date][$workScheduleWorkingPattern->id] ?? null;
                    });
                    return [
                        'date' => $date,
                        'schedules' => $schedules,
                    ];
                })->all();

                $office->periods = $periods;

                return $office;
            })->all();



            // return $offices;

            $startDate = $workSchedule->start_date;
            $endDate = $workSchedule->end_date;

            return Excel::download(new WorkScheduleByOfficeExport($offices, $workScheduleWorkingPatterns, $startDate, $endDate), 'REKAPITULASI JADWAL KERJA PERIODE ' . $startDate . ' - ' . $endDate . '.xlsx');

            // return view('work-schedule.export.export-by-office', [
            //     'offices' => $offices,
            //     'work_schedule_working_patterns' => $workScheduleWorkingPatterns,
            // ]);

            // return Excel::download(new AerplusDailySalaryExport($startDate, $endDate, $dailySalaries), 'Rekapitulasi Gaji Harian' . $startDate . ' - ' . $endDate . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
