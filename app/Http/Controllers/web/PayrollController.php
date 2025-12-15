<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DailySalary;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\MagentaDailySalarySetting;
use App\Models\Office;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('payroll.index');
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

    /**
     * Generate Daily
     */
    public function indexMonthly(Request $request)
    {
        // Generated
        $currentDate = date('Y-m-d');
        $salaryStartDayPeriod = 26;
        $salaryStartMonthPeriod = $request->query('month') ?? date('m');
        $salaryStartYearPeriod = $request->query('year') ?? date('Y');
        $companyId = $request->query('company_id');
        $staffOnly = $request->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;
        $AERPLUS_ID = 6;

        if ($salaryStartMonthPeriod == null || $salaryStartYearPeriod == null) {
            return response()->json([
                'message' => 'Bulan dan tahun diperlukan',
            ], 400);
        }

        $carbonDate = Carbon::parse($salaryStartYearPeriod . '-' . $salaryStartMonthPeriod . '-' . $salaryStartDayPeriod);

        $startDate = $carbonDate->subMonth()->toDateString();
        $endDate = $carbonDate->addMonth()->subDay()->toDateString();

        $previousStartDate = Carbon::parse($startDate)->subMonth()->toDateString();
        $previousEndDate = Carbon::parse($endDate)->subMonth()->toDateString();

        // return [$previousStartDate, $previousEndDate];

        // $staffOnly = false;
        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $userGroup = auth()->user()->group ?? null;
        $haveAllCompanyPermissions = $userGroup->have_all_company_permissions ?? false;
        $companyPermissions = json_decode($userGroup->company_permissions ?? "[]");

        if ($haveAllCompanyPermissions == false && count($companyPermissions) < 1) {
            return response('<span style="font-family:courier, courier new, serif;">ERROR 403 FORBIDDEN: YOU DONT HAVE ACCESS TO ANY OF THESE COMPANIES</span>', 200)
                ->header('Content-Type', 'text/html');
            // return view();
        }

        $companies = [];
        if ($haveAllCompanyPermissions == true) {
            $companies = Company::all();
        } else {
            $companies = Company::whereIn('id', $companyPermissions)->get();
        }

        if (!isset($companyId) || $companyId == "") {
            $companyId = collect($companies)->first()->id ?? null;
        }

        // return $companyId;

        // return response()->json([
        //     'have_staff_permission' => $haveStaffPermission,
        //     'staff_only' => $staffOnly,
        // ]);
        // return $haveStaffPermission;

        $employeesHasSalariesCollection = Employee::whereHas('salaries', function (Builder $q) use ($startDate, $endDate) {
            return $q->where('start_date', $startDate)->where('end_date', $endDate);
        })->with(['salaries' => function ($q) use ($startDate, $endDate) {
            return $q->where('start_date', $startDate)->where('end_date', $endDate);
        }]);

        if ($companyId == $AERPLUS_ID) {
            $employeesHasSalariesCollection->where('type', 'staff');
        } else {
            if (!$haveStaffPermission) {
                $employeesHasSalariesCollection->where('type', '!=', 'staff');
            } else {
                if ($staffOnly) {
                    $employeesHasSalariesCollection->where('type', 'staff');
                }
            }
        }


        if (isset($companyId) && $companyId !== "") {
            // return $companyId;
            $employeesHasSalariesCollection->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        // return 'no company id';

        $employeesHasSalariesCollection = $employeesHasSalariesCollection
            ->get()
            ->filter(function ($employee) {
                return count($employee->salaries) > 0;
            });

        // ? FIX SALARIES -----------------
        $fixSalaries = $employeesHasSalariesCollection->map(function ($employee) {
            $salaryItems = [];
            $salary = null;
            if (isset($employee->salaries[0])) {
                $salary = $employee->salaries[0];
                $salaryItems = $salary->items;
            }

            $incomes = [];

            $presenceIncentiveAmount = collect($salaryItems)->where('salary_type', 'presence_incentive')->first()->amount ?? 0;

            if ($employee->type == "staff") {
                $incomes = collect($salaryItems)->where('type', 'income')->where('salary_type', '!=', 'presence_incentive')->all();
            } else if ($employee->type == "non_staff") {
                $incomes = collect($salaryItems)->where('type', 'income')->all();
            }

            $deductions = collect($salaryItems)->where('type', 'deduction')->all();

            $totalIncomes = collect($incomes)->sum('amount');
            $totalDeductions = collect($deductions)->sum('amount');
            $totalTakeHomePay = $totalIncomes - $totalDeductions;

            return [
                'salary' => $salary,
                'employee' => $employee,
                'incomes' => $incomes,
                'deductions' => $deductions,
                'attributes' => [
                    'presence_incentive_amount' => $presenceIncentiveAmount,
                ],
                'summary' => [
                    'total_incomes' => $totalIncomes,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $totalTakeHomePay,
                ],
            ];
        })->all();

        $employeesHasSalariesIds = $employeesHasSalariesCollection->pluck('id')->all();
        // Summary
        $totalTakeHomePaySalaries = collect($fixSalaries)->sum(function ($salary) {
            return $salary['summary']['take_home_pay'];
        });

        // -------------------------

        $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', date('Y'))->get();
        // $leaveQuota = 12;

        $employees = Employee::with([
            'salaries' => function ($q) use ($previousStartDate, $previousEndDate) {
                $q->with(['items'])->where('start_date', $previousStartDate)->where('end_date', $previousEndDate)->orderBy('id', 'desc');
            },
            'attendances' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
            },
            'activeWorkingPatterns.items',
            'loans.items',
            // 'salaryComponents',
            'salaryComponents' => function ($q) use ($currentDate) {
                $q->orderBy('employee_salary_component.id', 'desc')->orderBy('employee_salary_component.effective_date', 'desc');
                // $q->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
                // $q->whereRaw('employee_salary_component.effective_date = ' . '"' . $year . '-01-01"')->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
            },
            'leave',
            'activeCareer.jobTitle',
            'leaveApplications' => function ($q) {
                $q->where('approval_status', 'approved')->where('leave_category_id', 7);
            }
        ]);

        if ($companyId == $AERPLUS_ID) {
            $employees->where('type', 'staff');
        } else {
            if (!$haveStaffPermission) {
                $employees->where('type', '!=', 'staff');
            } else {
                if ($staffOnly) {
                    $employees->where('type', 'staff');
                }
            }
        }

        if (isset($companyId) && $companyId !== "") {
            // return $companyId;
            $employees->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        $employees = $employees
            ->where(
                'active',
                1
            )
            ->whereNotIn('id', $employeesHasSalariesIds)
            ->get();

        // return $employees;

        $eventCalendars = EventCalendar::all();

        $startDateAttendancePeriod = $startDate;
        $endDateAttendancePeriod = $endDate;
        if ($startDate == '2023-03-26' && $endDate == '2023-04-25') {
            $endDateAttendancePeriod = '2023-04-15';
        }
        $periodDates = $this->getDatesFromRange($startDateAttendancePeriod, $endDateAttendancePeriod);

        // ? GENERATED SALARIES --------------
        $generatedSalaries = collect($employees)->map(function ($employee) use ($periodDates, $eventCalendars, $startDate, $endDate, $massLeaves) {
            $properties = [];

            $isGetPresenceIncentive = false;

            $attendances = $employee->attendances;
            $workingPatterns = $employee->activeWorkingPatterns;
            $salaryComponents = $employee->salaryComponents;
            $loans = $employee->loans;
            // $leave = $employee->leave;
            $totalLoans = collect($loans)->sum('amount');

            $mappedLoanItems = collect($loans)->flatMap(function ($loan) {
                return $loan['items'];
            });

            // $loanItems = $mappedLoanItems->all();

            $currentMonthLoanItems = $mappedLoanItems->where('payment_date', $endDate)->all();

            $salaryComponentTypes = ['gaji_pokok', 'tunjangan'];

            $incomes = [];
            $workDaysLength = 0;

            foreach ($salaryComponentTypes as $salaryComponentType) {
                $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();

                if (isset($salaryComponent)) {
                    $amount = $salaryComponent['pivot']['amount'] ?? 0;
                    // if($employee->start_work_date >= $startDate){
                    //     $workDaysLength = Carbon::parse($employee->start_work_date)->diffInDays(Carbon::parse($endDate)->subDay());
                    //     $amount = ($amount / 25) * $workDaysLength;
                    // }

                    array_push($incomes, [
                        'name' => $salaryComponent['name'] ?? '',
                        'type' => $salaryComponent['salary_type'] ?? '',
                        'amount' => $amount,
                    ]);
                }
            }

            $thrComponent = collect($salaryComponents)
                ->where('salary_type', 'thr')
                ->filter(function ($component) use ($endDate) {
                    $effectiveDateMonth = Carbon::parse($component->pivot->effective_date)->month;
                    $endDateMonth = Carbon::parse($endDate)->month;
                    return $effectiveDateMonth == $endDateMonth;
                })->first();

            if (isset($thrComponent)) {
                array_push($incomes, [
                    'name' => $thrComponent['name'] ?? '',
                    'type' => $thrComponent['salary_type'] ?? '',
                    'amount' => $thrComponent['pivot']['amount'] ?? 0,
                ]);
            }


            // $incomes = collect($salaryComponents)
            //     ->where('type', 'income')
            //     ->whereNotIn('salary_type', ['uang_harian', 'lembur', 'bonus', 'thr'])
            //     ->map(function ($income) {
            //         return [
            //             'name' => $income['name'],
            //             'type' => $income['salary_type'],
            //             'amount' => $income['pivot']['amount'],
            //         ];
            //     })->all();
            $deductions = collect($salaryComponents)
                ->where('type', 'deduction')
                ->map(function ($income) {
                    return [
                        'name' => $income['name'],
                        'type' => $income['salary_type'],
                        'amount' => $income['pivot']['amount'],
                    ];
                })->all();

            $activeWorkingPattern = collect($workingPatterns)->first();

            if ($activeWorkingPattern == null) {
                $properties[] = [
                    'color' => 'danger',
                    'text' => '<i class="bi bi-x-lg align-middle me-2 text-danger"></i><span class="align-middle">Pola Kerja</span>',
                ];
            }

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
                                // array_push($notPresentDays, [
                                //     'date' => $date,
                                //     'description' => 'Terlambat (' . $timeLate . ' menit)',
                                //     'time_late' => $timeLate,
                                //     'type' => 'late',
                                // ]);

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

            if ($isGetPresenceIncentive && ($employee->start_work_date <= $startDate)) {
                if ($employee->type == "non_staff") {
                    $presenceIncentiveAmount = $employeeWage['daily'] * 2;
                } else if ($employee->type == "staff") {
                    // $presenceIncentiveAmount = 250000;
                    $presenceIncentiveAmount = 100000;
                }
                $incomes = collect($incomes)->push([
                    'name' => 'Tunjangan Kehadiran',
                    'type' => 'presence_incentive',
                    'amount' => $presenceIncentiveAmount,
                ])->all();
            }
            // End: Presence Incentive (Insentif Kehadiran)

            // Loan
            if (count($currentMonthLoanItems) > 0) {
                $loanComponents = collect($currentMonthLoanItems)->map(function ($loanItem) {
                    return [
                        'name' => 'Kasbon',
                        'type' => 'loan',
                        'loan_id' => $loanItem['id'],
                        'amount' => $loanItem['basic_payment'],
                    ];
                })->all();

                $deductions = collect($deductions)->concat($loanComponents)->all();
            }
            // End: Loan

            // Excess Leave
            $noStatusAttendanceCount = collect($notPresentDays)->where('type', 'no_status')->count();
            $excessLeave = 0;

            // ? LEAVE APPLICATION
            $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                return explode(',', $leaveApplicationDates);
            })->all();

            $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);
            // $leavePeriod = new CarbonPeriod('2022-01-01', '2022-12-30');
            // $leavePeriodDates = [];
            // foreach ($leavePeriod as $key => $date) {
            //     $leavePeriodDates[] = $date->format('Y-m-d');
            // }
            $leavePeriodStartDate = date('Y-01-01');
            $leavePeriodEndDate = date('Y-12-t');

            $leaveQuota = $employee->leave->total ?? 0;

            $lengthOfWorking = Carbon::parse($employee->start_work_date)->diffInYears($leavePeriodStartDate);

            $leaveApplicationsCount = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($leavePeriodStartDate, $leavePeriodEndDate) {
                return $date >= $leavePeriodStartDate && $date <= $leavePeriodEndDate;
            })->count();

            $massLeavesCount = collect($massLeaves)->count();

            $takenLeave = $leaveApplicationsCount + $massLeavesCount;
            if ($lengthOfWorking < 1) {
                $takenLeave = 0;
            }

            if ($takenLeave > 12) {
                $takenLeave = 12;
            }

            $leave = [
                'total' => $leaveQuota,
                'taken' => $takenLeave,
            ];
            // ? END: LEAVE APPLICATION

            $leaveApplicationsDates = [];

            if (isset($leave['total']) && isset($leave['taken'])) {
                $remainingLeave = $leave['total'] - $leave['taken'];
                // round($basicSalary->value / 26) * $item->excess_leave;
                $leaveApplicationCount = 0;
                $finalRemainingLeave = $remainingLeave - $noStatusAttendanceCount;
                $excessLeave = 0;
                if ($finalRemainingLeave <= 0) {
                    $excessLeave = abs($finalRemainingLeave);
                    $leaveApplicationCount = $remainingLeave;
                } else if ($finalRemainingLeave > 0) {
                    $leaveApplicationCount = $noStatusAttendanceCount;
                }

                // Determine leave application dates
                $leaveApplicationDates = [];
                for ($i = 0; $i < $leaveApplicationCount; $i++) {
                    $leaveApplicationDates[] = $noStatusAttendanceDates[$i];
                }

                $basicSalary = collect($incomes)->where('type', 'gaji_pokok')->first();

                $basicSalaryAmount = $basicSalary['amount'] ?? 0;
                // if ($basicSalary !== null && isset($basicSalary['amount'])) {
                //     $basicSalaryAmount = $basicSalary['amount'];
                // }
                $excessLeaveCharge = ($basicSalaryAmount / 25) * $excessLeave;

                if ($excessLeave > 0) {
                    $deductions = collect($deductions)->push([
                        'name' => 'Kelebihan Cuti ' .  '(' . $excessLeave . ' Hari)',
                        'type' => 'excess_leave',
                        'type_amount' => $excessLeave,
                        'type_unit' => 'day',
                        'amount' => round($excessLeaveCharge),
                        'remaining_leave' => $remainingLeave,
                        'application_count' => $leaveApplicationCount,
                        'application_dates' => $leaveApplicationDates,
                    ])->all();
                }
            }
            // if ($leave !== null) {
            // }
            // End: Excess Leave

            // Calculate new employee salaries

            $incomes = collect($incomes)->filter(function ($income) {
                return $income['type'] !== 'gaji_pokok';
                // && $income['type'] !== 'tunjangan';
            })->values()->all();
            $underMonthEmployeeSalaryTypes = ['gaji_pokok'];
            foreach ($underMonthEmployeeSalaryTypes as $salaryComponentType) {
                $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();

                if (isset($salaryComponent)) {
                    $amount = $salaryComponent['pivot']['amount'] ?? 0;
                    if ($employee->start_work_date >= $startDate) {
                        $workDaysLength = $presentDaysCount;

                        if ($workDaysLength > 25) {
                            $workDaysLength = 25;
                        }

                        $properties[] = [
                            'color' => 'primary',
                            'text' => '<i class="bi bi-layers-half align-middle text-primary me-1"></i><span class="align-middle">Prorata ' . '(' . $workDaysLength . ' Hari Kerja)' . '</span>',
                        ];

                        $amount = ($amount / 25) * $workDaysLength;
                    }

                    array_push($incomes, [
                        'name' => $salaryComponent['name'] ?? '',
                        'type' => $salaryComponent['salary_type'] ?? '',
                        'amount' => $amount,
                    ]);
                }
            }

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

                    $deductions = collect($deductions)->push([
                        'name' => 'Denda Keterlambatan ' .  '(' . $totalTimeLate . ' Menit)',
                        'type' => 'late',
                        'type_amount' => $totalTimeLate,
                        'type_unit' => 'minute',
                        'amount' => $latePenaltyAmount,
                    ])->all();
                }

                $notClockInAttendancesCount = count($notClockInAttendances);
                $notClockOutAttendancesCount = count($notClockOutAttendances);

                // if ($notClockInAttendancesCount > 0 || $notClockOutAttendancesCount > 0) {
                //     $notClockAttendancePenaltyAmount = ($notClockInAttendancesCount + $notClockOutAttendancesCount) * 50000;
                //     $deductions = collect($deductions)->push([
                //         'name' => 'Denda Tidak Tap-in / Tap-out ' .  '(' . ($notClockInAttendancesCount + $notClockOutAttendancesCount) . ' Kali)',
                //         'type' => 'not_clock_in_out',
                //         'type_amount' => ($notClockInAttendancesCount + $notClockOutAttendancesCount),
                //         'type_unit' => 'time',
                //         'amount' => $notClockAttendancePenaltyAmount,
                //     ])->all();
                // }
                // if ($notClockOutAttendancesCount > 0) {
                //     $notClockAttendancePenaltyAmount = ($notClockOutAttendancesCount) * 50000;
                //     $deductions = collect($deductions)->push([
                //         'name' => 'Denda Tidak Tap-out ' .  '(' . ($notClockOutAttendancesCount) . ' Kali)',
                //         'type' => 'not_clock_in_out',
                //         'type_amount' => ($notClockOutAttendancesCount),
                //         'type_unit' => 'time',
                //         'amount' => $notClockAttendancePenaltyAmount,
                //     ])->all();
                // }
            }

            // Sorting components
            $incomes = collect($incomes)->sortByDesc('amount')->values()->all();
            // $tempIncomes = $incomes;
            // if ($employee->type == "staff") {
            //     $incomes = collect($incomes)->where('type', '!=', 'presence_incentive')->sortByDesc('amount')->values()->all();
            // } else if ($employee->type == "non_staff") {
            //     $incomes = collect($incomes)->sortByDesc('amount')->values()->all();
            // }

            $deductions = collect($deductions)->sortByDesc('amount')->values()->all();

            // Summary
            $totalIncomes = 0;
            if ($employee->type == "staff") {
                $totalIncomes = collect($incomes)->where('type', '!=', 'presence_incentive')->sum('amount');
            } else if ($employee->type == "non_staff") {
                $totalIncomes = collect($incomes)->sum('amount');
            }
            $totalDeductions = collect($deductions)->sum('amount');
            $takeHomePay = $totalIncomes - $totalDeductions;

            return [
                'employee' => collect($employee)->except('attendances')->all(),
                'length_of_working' => $lengthOfWorking,
                'work_days_length' => $workDaysLength,
                'leave_application_dates' => $leaveApplicationDates,
                'leave' => $leave,
                'total_time_late' => $totalTimeLate,
                'not_clock_in_attendances_count' => count($notClockInAttendances),
                'not_clock_out_attendances_count' => count($notClockOutAttendances),
                // 'periods' => $periods,
                'not_present_days' => $notPresentDays,
                'late_days' => $lateDays,
                'working_pattern_day_status' => $workingPatternDayStatus,
                'incomes' => $incomes,
                'deductions' => $deductions,
                'attributes' => [
                    'presence_incentive_amount' => $presenceIncentiveAmount,
                    'is_get_presence_incentive' => $isGetPresenceIncentive,
                    'excess_leave' => $excessLeave,
                ],
                'summary' => [
                    'total_incomes' => $totalIncomes,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $takeHomePay,
                    'total_loans' => $totalLoans,
                ],
                'loans' => $loans,
                'loan_items' => $currentMonthLoanItems,
                'properties' => $properties,
            ];
        })->all();

        // return response()->json(collect($generatedSalaries)->filter(function ($salary) {
        //     return $salary['employee']['id'] == 731;
        // })->first());

        // Generated
        $totalTakeHomePayGeneratedSalaries = collect($generatedSalaries)->sum(function ($salary) {
            return $salary['summary']['take_home_pay'];
        });

        // ?SALARIES SUMMARY (REKAP)
        $salariesSummaryEmployees = Employee::query()->whereHas('salaries', function ($query) use ($startDate, $endDate) {
            $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
        })
            ->with([
                'activeCareer.jobTitle',
                'office.division.company',
                'bankAccounts',
                'loans' => function ($q) use ($endDate) {
                    $q->with(['items' => function ($itemQuery) use ($endDate) {
                        $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
                    }]);
                },
                'salaries' => function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', $startDate)->where('end_date', $endDate);
                }
            ]);

        if (!$haveStaffPermission) {
            $salariesSummaryEmployees->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $salariesSummaryEmployees->where('type', 'staff');
            }
        }

        $salariesSummaryEmployees = $salariesSummaryEmployees->get()
            ->map(function ($employee) use ($startDate, $endDate) {
                $basicSalary = 0;
                $positionAllowance = 0;
                $attendanceAllowance = 0;
                $currentMonthLoans = 0;
                $excessLeave = 0;
                $total = 0;
                $bankAccountNumber = null;

                $defaultBankAccount = collect($employee->bankAccounts)->where('default', 1)->first();
                if ($defaultBankAccount !== null) {
                    $bankAccountNumber = $defaultBankAccount['account_number'];
                }

                $totalLoan = collect($employee->loans)->sum('amount');
                // $totalPaidLoan = collect($employee->loans)->pluck('items')->filter(function ($item) {
                //     return $item->salary_item_count > 0;
                // })->sum('amount');
                $totalPaidLoan = collect($employee->loans)->flatMap(function ($loan) {
                    return $loan->items;
                })->filter(function ($item) {
                    return $item->salary_item_count > 0;
                })->sum('basic_payment');

                $remainingLoan = $totalLoan - $totalPaidLoan;

                $currentMonthSingleLoan = collect($employee->loans)->whereBetween('effective_date', [$startDate, $endDate])->sum('amount');

                $salariesDataCount = collect($employee->salaries)->count();
                // if ($salariesDataCount > 0) {
                //     $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                //         return $salary->items;
                //     });
                //     $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                //     $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                //     $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                //     $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
                //     $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                //     $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                // }
                if ($salariesDataCount > 0) {
                    $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                        return $salary->items;
                    });
                    $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                    $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                    $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                    if ($employee->type == "staff") {
                        $attendanceAllowance = 0;
                    }
                    $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->where('type', 'deduction')->sum('amount');
                    $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                    // $total = ($basicSalary + $positionAllowance + $attendanceAllowance + $currentMonthSingleLoan) - ($currentMonthLoans + $excessLeave);
                    $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                }

                // Gaji Pokok
                $employee->basic_salary = $basicSalary;
                // Tunjangan
                $employee->position_allowance = $positionAllowance;
                // Insentif Kehadiran
                $employee->attendance_allowance = $attendanceAllowance;
                // Bruto
                $employee->bruto_salary = $basicSalary + $positionAllowance + $attendanceAllowance;
                // Piutang Awal
                $employee->total_loan = $totalLoan;
                // Piutang Pinjaman
                $employee->current_month_loan = $currentMonthSingleLoan;
                // Piutang Potongan
                $employee->loan = $currentMonthLoans;
                // Piutang Sisa
                $employee->remaining_loan = $remainingLoan;
                // Total Paid Loan
                $employee->total_paid_loan = $totalPaidLoan;
                // Kelebihan Cuti
                $employee->excess_leave = $excessLeave;
                // Netto
                $employee->total = $total;
                // Bank Account
                $employee->bank_account_number = $bankAccountNumber;

                return $employee;
            })->all();

        $company = Company::find($companyId);

        // return $salariesSummaryEmployees;

        return view('payrolls.monthly.index', [
            'company' => $company,
            'summary_salaries' => [
                'salaries' => $salariesSummaryEmployees,
            ],
            'fix_salaries' => [
                'salaries' => $fixSalaries,
                'total_employees' => count($fixSalaries),
                'total_take_home_pay' => $totalTakeHomePaySalaries,
            ],
            'generated_salaries' => [
                'salaries' => $generatedSalaries,
                'total_employees' => count($generatedSalaries),
                'total_take_home_pay' => $totalTakeHomePayGeneratedSalaries,
            ],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'salary_month' => $salaryStartMonthPeriod,
            'salary_year' => $salaryStartYearPeriod,
            'staff_only' => $staffOnly,
            'company_id' => $companyId,
            'have_staff_permission' => $haveStaffPermission,
            'companies' => $companies,
        ]);
    }

    /**
     * Generate Daily
     */
    public function indexMonthlyTest(Request $request)
    {
        // Generated
        $currentDate = date('Y-m-d');
        $salaryStartDayPeriod = 26;
        $salaryStartMonthPeriod = $request->query('month') ?? date('m');
        $salaryStartYearPeriod = $request->query('year') ?? date('Y');
        $companyId = $request->query('company_id');
        $staffOnly = $request->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;
        $AERPLUS_ID = 6;

        if ($salaryStartMonthPeriod == null || $salaryStartYearPeriod == null) {
            return response()->json([
                'message' => 'Bulan dan tahun diperlukan',
            ], 400);
        }

        $carbonDate = Carbon::parse($salaryStartYearPeriod . '-' . $salaryStartMonthPeriod . '-' . $salaryStartDayPeriod);

        $startDate = $carbonDate->subMonth()->toDateString();
        $endDate = $carbonDate->addMonth()->subDay()->toDateString();

        $previousStartDate = Carbon::parse($startDate)->subMonth()->toDateString();
        $previousEndDate = Carbon::parse($endDate)->subMonth()->toDateString();

        // return [$previousStartDate, $previousEndDate];

        // $staffOnly = false;
        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $userGroup = auth()->user()->group ?? null;
        $haveAllCompanyPermissions = $userGroup->have_all_company_permissions ?? false;
        $companyPermissions = json_decode($userGroup->company_permissions ?? "[]");

        if ($haveAllCompanyPermissions == false && count($companyPermissions) < 1) {
            return response('<span style="font-family:courier, courier new, serif;">ERROR 403 FORBIDDEN: YOU DONT HAVE ACCESS TO ANY OF THESE COMPANIES</span>', 200)
                ->header('Content-Type', 'text/html');
            // return view();
        }

        $companies = [];
        if ($haveAllCompanyPermissions == true) {
            $companies = Company::all();
        } else {
            $companies = Company::whereIn('id', $companyPermissions)->get();
        }

        if (!isset($companyId) || $companyId == "") {
            $companyId = collect($companies)->first()->id ?? null;
        }

        // return $companyId;

        // return response()->json([
        //     'have_staff_permission' => $haveStaffPermission,
        //     'staff_only' => $staffOnly,
        // ]);
        // return $haveStaffPermission;

        $employeesHasSalariesCollection = Employee::whereHas('salaries', function (Builder $q) use ($startDate, $endDate) {
            return $q->where('start_date', $startDate)->where('end_date', $endDate);
        })->with(['salaries' => function ($q) use ($startDate, $endDate) {
            return $q->where('start_date', $startDate)->where('end_date', $endDate);
        }]);

        if ($companyId == $AERPLUS_ID) {
            $employeesHasSalariesCollection->where('type', 'staff');
        } else {
            if (!$haveStaffPermission) {
                $employeesHasSalariesCollection->where('type', '!=', 'staff');
            } else {
                if ($staffOnly) {
                    $employeesHasSalariesCollection->where('type', 'staff');
                }
            }
        }


        if (isset($companyId) && $companyId !== "") {
            // return $companyId;
            $employeesHasSalariesCollection->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        // return 'no company id';

        $employeesHasSalariesCollection = $employeesHasSalariesCollection
            ->get()
            ->filter(function ($employee) {
                return count($employee->salaries) > 0;
            });

        // ? FIX SALARIES -----------------
        $fixSalaries = $employeesHasSalariesCollection->map(function ($employee) {
            $salaryItems = [];
            $salary = null;
            if (isset($employee->salaries[0])) {
                $salary = $employee->salaries[0];
                $salaryItems = $salary->items;
            }

            $incomes = [];

            $presenceIncentiveAmount = collect($salaryItems)->where('salary_type', 'presence_incentive')->first()->amount ?? 0;

            if ($employee->type == "staff") {
                $incomes = collect($salaryItems)->where('type', 'income')->where('salary_type', '!=', 'presence_incentive')->all();
            } else if ($employee->type == "non_staff") {
                $incomes = collect($salaryItems)->where('type', 'income')->all();
            }

            $deductions = collect($salaryItems)->where('type', 'deduction')->all();

            $totalIncomes = collect($incomes)->sum('amount');
            $totalDeductions = collect($deductions)->sum('amount');
            $totalTakeHomePay = $totalIncomes - $totalDeductions;

            return [
                'salary' => $salary,
                'employee' => $employee,
                'incomes' => $incomes,
                'deductions' => $deductions,
                'attributes' => [
                    'presence_incentive_amount' => $presenceIncentiveAmount,
                ],
                'summary' => [
                    'total_incomes' => $totalIncomes,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $totalTakeHomePay,
                ],
            ];
        })->all();

        $employeesHasSalariesIds = $employeesHasSalariesCollection->pluck('id')->all();
        // Summary
        $totalTakeHomePaySalaries = collect($fixSalaries)->sum(function ($salary) {
            return $salary['summary']['take_home_pay'];
        });

        // -------------------------

        $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', date('Y'))->get();
        // $leaveQuota = 12;

        $employees = Employee::with([
            'salaries' => function ($q) use ($previousStartDate, $previousEndDate) {
                $q->with(['items'])->where('start_date', $previousStartDate)->where('end_date', $previousEndDate)->orderBy('id', 'desc');
            },
            'attendances' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
            },
            'activeWorkingPatterns.items',
            'loans.items',
            // 'salaryComponents',
            'salaryComponents' => function ($q) use ($currentDate) {
                $q->orderBy('employee_salary_component.id', 'desc')->orderBy('employee_salary_component.effective_date', 'desc');
                // $q->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
                // $q->whereRaw('employee_salary_component.effective_date = ' . '"' . $year . '-01-01"')->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
            },
            'leave',
            'activeCareer.jobTitle',
            'leaveApplications' => function ($q) {
                $q->where('approval_status', 'approved')->where('leave_category_id', 7);
            }
        ]);

        if ($companyId == $AERPLUS_ID) {
            $employees->where('type', 'staff');
        } else {
            if (!$haveStaffPermission) {
                $employees->where('type', '!=', 'staff');
            } else {
                if ($staffOnly) {
                    $employees->where('type', 'staff');
                }
            }
        }

        if (isset($companyId) && $companyId !== "") {
            // return $companyId;
            $employees->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        $employees = $employees
            ->where(
                'active',
                1
            )
            ->whereNotIn('id', $employeesHasSalariesIds)
            ->get();

        // return $employees;

        $eventCalendars = EventCalendar::all();

        $startDateAttendancePeriod = $startDate;
        $endDateAttendancePeriod = $endDate;
        if ($startDate == '2023-03-26' && $endDate == '2023-04-25') {
            $endDateAttendancePeriod = '2023-04-15';
        }
        $periodDates = $this->getDatesFromRange($startDateAttendancePeriod, $endDateAttendancePeriod);

        // ? GENERATED SALARIES --------------
        $generatedSalaries = collect($employees)->map(function ($employee) use ($periodDates, $eventCalendars, $startDate, $endDate, $massLeaves) {
            $properties = [];

            $isGetPresenceIncentive = false;

            $attendances = $employee->attendances;
            $workingPatterns = $employee->activeWorkingPatterns;
            $salaryComponents = $employee->salaryComponents;
            $loans = $employee->loans;
            // $leave = $employee->leave;
            $totalLoans = collect($loans)->sum('amount');

            $mappedLoanItems = collect($loans)->flatMap(function ($loan) {
                return $loan['items'];
            });

            // $loanItems = $mappedLoanItems->all();

            $currentMonthLoanItems = $mappedLoanItems->where('payment_date', $endDate)->all();

            $salaryComponentTypes = ['gaji_pokok', 'tunjangan'];

            $incomes = [];
            $workDaysLength = 0;

            foreach ($salaryComponentTypes as $salaryComponentType) {
                $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();

                if (isset($salaryComponent)) {
                    $amount = $salaryComponent['pivot']['amount'] ?? 0;
                    // if($employee->start_work_date >= $startDate){
                    //     $workDaysLength = Carbon::parse($employee->start_work_date)->diffInDays(Carbon::parse($endDate)->subDay());
                    //     $amount = ($amount / 25) * $workDaysLength;
                    // }

                    array_push($incomes, [
                        'name' => $salaryComponent['name'] ?? '',
                        'type' => $salaryComponent['salary_type'] ?? '',
                        'amount' => $amount,
                    ]);
                }
            }

            $thrComponent = collect($salaryComponents)
                ->where('salary_type', 'thr')
                ->filter(function ($component) use ($endDate) {
                    $effectiveDateMonth = Carbon::parse($component->pivot->effective_date)->month;
                    $endDateMonth = Carbon::parse($endDate)->month;
                    return $effectiveDateMonth == $endDateMonth;
                })->first();

            if (isset($thrComponent)) {
                array_push($incomes, [
                    'name' => $thrComponent['name'] ?? '',
                    'type' => $thrComponent['salary_type'] ?? '',
                    'amount' => $thrComponent['pivot']['amount'] ?? 0,
                ]);
            }


            // $incomes = collect($salaryComponents)
            //     ->where('type', 'income')
            //     ->whereNotIn('salary_type', ['uang_harian', 'lembur', 'bonus', 'thr'])
            //     ->map(function ($income) {
            //         return [
            //             'name' => $income['name'],
            //             'type' => $income['salary_type'],
            //             'amount' => $income['pivot']['amount'],
            //         ];
            //     })->all();
            $deductions = collect($salaryComponents)
                ->where('type', 'deduction')
                ->map(function ($income) {
                    return [
                        'name' => $income['name'],
                        'type' => $income['salary_type'],
                        'amount' => $income['pivot']['amount'],
                    ];
                })->all();

            $activeWorkingPattern = collect($workingPatterns)->first();

            if ($activeWorkingPattern == null) {
                $properties[] = [
                    'color' => 'danger',
                    'text' => '<i class="bi bi-x"></i><span>Pola Kerja</span>',
                ];
            }

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
                                // array_push($notPresentDays, [
                                //     'date' => $date,
                                //     'description' => 'Terlambat (' . $timeLate . ' menit)',
                                //     'time_late' => $timeLate,
                                //     'type' => 'late',
                                // ]);

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
                $incomes = collect($incomes)->push([
                    'name' => 'Tunjangan Kehadiran',
                    'type' => 'presence_incentive',
                    'amount' => $presenceIncentiveAmount,
                ])->all();
            }
            // End: Presence Incentive (Insentif Kehadiran)

            // Loan
            if (count($currentMonthLoanItems) > 0) {
                $loanComponents = collect($currentMonthLoanItems)->map(function ($loanItem) {
                    return [
                        'name' => 'Kasbon',
                        'type' => 'loan',
                        'loan_id' => $loanItem['id'],
                        'amount' => $loanItem['basic_payment'],
                    ];
                })->all();

                $deductions = collect($deductions)->concat($loanComponents)->all();
            }
            // End: Loan

            // Excess Leave
            $noStatusAttendanceCount = collect($notPresentDays)->where('type', 'no_status')->count();
            $excessLeave = 0;

            // ? LEAVE APPLICATION
            $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                return explode(',', $leaveApplicationDates);
            })->all();

            $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);
            // $leavePeriod = new CarbonPeriod('2022-01-01', '2022-12-30');
            // $leavePeriodDates = [];
            // foreach ($leavePeriod as $key => $date) {
            //     $leavePeriodDates[] = $date->format('Y-m-d');
            // }
            $leavePeriodStartDate = date('Y-01-01');
            $leavePeriodEndDate = date('Y-12-t');

            $leaveQuota = $employee->leave->total ?? 0;

            $lengthOfWorking = Carbon::parse($employee->start_work_date)->diffInYears($leavePeriodStartDate);

            $leaveApplicationsCount = collect($leaveApplicationsUniqueDates)->filter(function ($date) use ($leavePeriodStartDate, $leavePeriodEndDate) {
                return $date >= $leavePeriodStartDate && $date <= $leavePeriodEndDate;
            })->count();

            $massLeavesCount = collect($massLeaves)->count();

            $takenLeave = $leaveApplicationsCount + $massLeavesCount;
            if ($lengthOfWorking < 1) {
                $takenLeave = 0;
            }

            if ($takenLeave > 12) {
                $takenLeave = 12;
            }

            $leave = [
                'total' => $leaveQuota,
                'taken' => $takenLeave,
            ];
            // ? END: LEAVE APPLICATION

            $leaveApplicationsDates = [];

            if (isset($leave['total']) && isset($leave['taken'])) {
                $remainingLeave = $leave['total'] - $leave['taken'];
                // round($basicSalary->value / 26) * $item->excess_leave;
                $leaveApplicationCount = 0;
                $finalRemainingLeave = $remainingLeave - $noStatusAttendanceCount;
                $excessLeave = 0;
                if ($finalRemainingLeave <= 0) {
                    $excessLeave = abs($finalRemainingLeave);
                    $leaveApplicationCount = $remainingLeave;
                } else if ($finalRemainingLeave > 0) {
                    $leaveApplicationCount = $noStatusAttendanceCount;
                }

                // Determine leave application dates
                $leaveApplicationDates = [];
                for ($i = 0; $i < $leaveApplicationCount; $i++) {
                    $leaveApplicationDates[] = $noStatusAttendanceDates[$i];
                }

                $basicSalary = collect($incomes)->where('type', 'gaji_pokok')->first();

                $basicSalaryAmount = $basicSalary['amount'] ?? 0;
                // if ($basicSalary !== null && isset($basicSalary['amount'])) {
                //     $basicSalaryAmount = $basicSalary['amount'];
                // }
                $excessLeaveCharge = ($basicSalaryAmount / 25) * $excessLeave;

                if ($excessLeave > 0) {
                    $deductions = collect($deductions)->push([
                        'name' => 'Kelebihan Cuti ' .  '(' . $excessLeave . ' Hari)',
                        'type' => 'excess_leave',
                        'type_amount' => $excessLeave,
                        'type_unit' => 'day',
                        'amount' => round($excessLeaveCharge),
                        'remaining_leave' => $remainingLeave,
                        'application_count' => $leaveApplicationCount,
                        'application_dates' => $leaveApplicationDates,
                    ])->all();
                }
            }
            // if ($leave !== null) {
            // }
            // End: Excess Leave

            // Calculate new employee salaries

            $incomes = collect($incomes)->filter(function ($income) {
                return $income['type'] !== 'gaji_pokok';
                // && $income['type'] !== 'tunjangan';
            })->values()->all();
            $underMonthEmployeeSalaryTypes = ['gaji_pokok'];
            $formula = "";
            foreach ($underMonthEmployeeSalaryTypes as $salaryComponentType) {
                $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();

                if (isset($salaryComponent)) {
                    $amount = $salaryComponent['pivot']['amount'] ?? 0;
                    if ($employee->start_work_date >= $startDate) {
                        $properties[] = [
                            'color' => 'primary',
                            'text' => '<i class="bi bi-layers-half"></i><span>Prorate</span>',
                        ];

                        $workDaysLength = $presentDaysCount;
                        if ($workDaysLength > 25) {
                            $workDaysLength = 25;
                        }

                        $formula = '(' . $amount . ' / ' . '25' . ')' . ' * ' . $workDaysLength;
                        $amount = ($amount / 25) * $workDaysLength;
                    }

                    array_push($incomes, [
                        'name' => $salaryComponent['name'] ?? '',
                        'type' => $salaryComponent['salary_type'] ?? '',
                        'amount' => $amount,
                    ]);
                }
            }

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

                    $deductions = collect($deductions)->push([
                        'name' => 'Denda Keterlambatan ' .  '(' . $totalTimeLate . ' Menit)',
                        'type' => 'late',
                        'type_amount' => $totalTimeLate,
                        'type_unit' => 'minute',
                        'amount' => $latePenaltyAmount,
                    ])->all();
                }

                $notClockInAttendancesCount = count($notClockInAttendances);
                $notClockOutAttendancesCount = count($notClockOutAttendances);

                // if ($notClockInAttendancesCount > 0 || $notClockOutAttendancesCount > 0) {
                //     $notClockAttendancePenaltyAmount = ($notClockInAttendancesCount + $notClockOutAttendancesCount) * 50000;
                //     $deductions = collect($deductions)->push([
                //         'name' => 'Denda Tidak Tap-in / Tap-out ' .  '(' . ($notClockInAttendancesCount + $notClockOutAttendancesCount) . ' Kali)',
                //         'type' => 'not_clock_in_out',
                //         'type_amount' => ($notClockInAttendancesCount + $notClockOutAttendancesCount),
                //         'type_unit' => 'time',
                //         'amount' => $notClockAttendancePenaltyAmount,
                //     ])->all();
                // }
                // if ($notClockOutAttendancesCount > 0) {
                //     $notClockAttendancePenaltyAmount = ($notClockOutAttendancesCount) * 50000;
                //     $deductions = collect($deductions)->push([
                //         'name' => 'Denda Tidak Tap-out ' .  '(' . ($notClockOutAttendancesCount) . ' Kali)',
                //         'type' => 'not_clock_in_out',
                //         'type_amount' => ($notClockOutAttendancesCount),
                //         'type_unit' => 'time',
                //         'amount' => $notClockAttendancePenaltyAmount,
                //     ])->all();
                // }
            }

            // Sorting components
            $incomes = collect($incomes)->sortByDesc('amount')->values()->all();
            // $tempIncomes = $incomes;
            // if ($employee->type == "staff") {
            //     $incomes = collect($incomes)->where('type', '!=', 'presence_incentive')->sortByDesc('amount')->values()->all();
            // } else if ($employee->type == "non_staff") {
            //     $incomes = collect($incomes)->sortByDesc('amount')->values()->all();
            // }

            $deductions = collect($deductions)->sortByDesc('amount')->values()->all();

            // Summary
            $totalIncomes = 0;
            if ($employee->type == "staff") {
                $totalIncomes = collect($incomes)->where('type', '!=', 'presence_incentive')->sum('amount');
            } else if ($employee->type == "non_staff") {
                $totalIncomes = collect($incomes)->sum('amount');
            }
            $totalDeductions = collect($deductions)->sum('amount');
            $takeHomePay = $totalIncomes - $totalDeductions;

            return [
                'employee' => collect($employee)->except('attendances')->all(),
                'length_of_working' => $lengthOfWorking,
                'work_days_length' => $workDaysLength,
                'leave_application_dates' => $leaveApplicationDates,
                'leave' => $leave,
                'total_time_late' => $totalTimeLate,
                'not_clock_in_attendances_count' => count($notClockInAttendances),
                'not_clock_out_attendances_count' => count($notClockOutAttendances),
                // 'periods' => $periods,
                'not_present_days' => $notPresentDays,
                'late_days' => $lateDays,
                'working_pattern_day_status' => $workingPatternDayStatus,
                'incomes' => $incomes,
                'deductions' => $deductions,
                'attributes' => [
                    'presence_incentive_amount' => $presenceIncentiveAmount,
                    'is_get_presence_incentive' => $isGetPresenceIncentive,
                    'excess_leave' => $excessLeave,
                ],
                'summary' => [
                    'total_incomes' => $totalIncomes,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $takeHomePay,
                    'total_loans' => $totalLoans,
                ],
                'loans' => $loans,
                'loan_items' => $currentMonthLoanItems,
                'properties' => $properties,
                'formula' => $formula,
            ];
        })->all();

        return response()->json(collect($generatedSalaries)->filter(function ($salary) {
            return $salary['employee']['id'] == 766;
        })->first());

        // Generated
        $totalTakeHomePayGeneratedSalaries = collect($generatedSalaries)->sum(function ($salary) {
            return $salary['summary']['take_home_pay'];
        });

        // ?SALARIES SUMMARY (REKAP)
        $salariesSummaryEmployees = Employee::query()->whereHas('salaries', function ($query) use ($startDate, $endDate) {
            $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
        })
            ->with([
                'activeCareer.jobTitle',
                'office.division.company',
                'bankAccounts',
                'loans' => function ($q) use ($endDate) {
                    $q->with(['items' => function ($itemQuery) use ($endDate) {
                        $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
                    }]);
                },
                'salaries' => function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', $startDate)->where('end_date', $endDate);
                }
            ]);

        if (!$haveStaffPermission) {
            $salariesSummaryEmployees->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $salariesSummaryEmployees->where('type', 'staff');
            }
        }

        $salariesSummaryEmployees = $salariesSummaryEmployees->get()
            ->map(function ($employee) use ($startDate, $endDate) {
                $basicSalary = 0;
                $positionAllowance = 0;
                $attendanceAllowance = 0;
                $currentMonthLoans = 0;
                $excessLeave = 0;
                $total = 0;
                $bankAccountNumber = null;

                $defaultBankAccount = collect($employee->bankAccounts)->where('default', 1)->first();
                if ($defaultBankAccount !== null) {
                    $bankAccountNumber = $defaultBankAccount['account_number'];
                }

                $totalLoan = collect($employee->loans)->sum('amount');
                // $totalPaidLoan = collect($employee->loans)->pluck('items')->filter(function ($item) {
                //     return $item->salary_item_count > 0;
                // })->sum('amount');
                $totalPaidLoan = collect($employee->loans)->flatMap(function ($loan) {
                    return $loan->items;
                })->filter(function ($item) {
                    return $item->salary_item_count > 0;
                })->sum('basic_payment');

                $remainingLoan = $totalLoan - $totalPaidLoan;

                $currentMonthSingleLoan = collect($employee->loans)->whereBetween('effective_date', [$startDate, $endDate])->sum('amount');

                $salariesDataCount = collect($employee->salaries)->count();
                // if ($salariesDataCount > 0) {
                //     $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                //         return $salary->items;
                //     });
                //     $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                //     $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                //     $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                //     $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
                //     $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                //     $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                // }
                if ($salariesDataCount > 0) {
                    $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                        return $salary->items;
                    });
                    $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                    $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                    $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                    if ($employee->type == "staff") {
                        $attendanceAllowance = 0;
                    }
                    $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->where('type', 'deduction')->sum('amount');
                    $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                    // $total = ($basicSalary + $positionAllowance + $attendanceAllowance + $currentMonthSingleLoan) - ($currentMonthLoans + $excessLeave);
                    $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                }

                // Gaji Pokok
                $employee->basic_salary = $basicSalary;
                // Tunjangan
                $employee->position_allowance = $positionAllowance;
                // Insentif Kehadiran
                $employee->attendance_allowance = $attendanceAllowance;
                // Bruto
                $employee->bruto_salary = $basicSalary + $positionAllowance + $attendanceAllowance;
                // Piutang Awal
                $employee->total_loan = $totalLoan;
                // Piutang Pinjaman
                $employee->current_month_loan = $currentMonthSingleLoan;
                // Piutang Potongan
                $employee->loan = $currentMonthLoans;
                // Piutang Sisa
                $employee->remaining_loan = $remainingLoan;
                // Total Paid Loan
                $employee->total_paid_loan = $totalPaidLoan;
                // Kelebihan Cuti
                $employee->excess_leave = $excessLeave;
                // Netto
                $employee->total = $total;
                // Bank Account
                $employee->bank_account_number = $bankAccountNumber;

                return $employee;
            })->all();

        $company = Company::find($companyId);

        // return $salariesSummaryEmployees;

        return view('payrolls.monthly.index', [
            'company' => $company,
            'summary_salaries' => [
                'salaries' => $salariesSummaryEmployees,
            ],
            'fix_salaries' => [
                'salaries' => $fixSalaries,
                'total_employees' => count($fixSalaries),
                'total_take_home_pay' => $totalTakeHomePaySalaries,
            ],
            'generated_salaries' => [
                'salaries' => $generatedSalaries,
                'total_employees' => count($generatedSalaries),
                'total_take_home_pay' => $totalTakeHomePayGeneratedSalaries,
            ],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'salary_month' => $salaryStartMonthPeriod,
            'salary_year' => $salaryStartYearPeriod,
            'staff_only' => $staffOnly,
            'company_id' => $companyId,
            'have_staff_permission' => $haveStaffPermission,
            'companies' => $companies,
        ]);
    }

    /**
     * Index Daily
     */
    public function indexDaily(Request $request)
    {
        // $year = date('Y');

        $year = $request->query('year') ?? date('Y');

        // return $year;
        // if ($request->query('year') !== null) {
        // }

        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $dailySalaries = DailySalary::where('type', 'regular')
            // ->limit(10)
            ->select(['id', 'start_date', 'end_date'])
            ->whereYear('end_date', $year)
            ->get()
            ->groupBy([function ($salary) {
                return (int) date('m', strtotime($salary->end_date)) - 1;
            }, function ($salary) {
                return $salary->start_date . '/' . $salary->end_date;
            }])->sort()
            ->all();

        $setting = MagentaDailySalarySetting::first();

        return view('payrolls.daily.index', [
            'setting' => $setting,
            'payslips' => $dailySalaries,
            'months' => $months,
            'year' => $year,
        ]);
    }

    /**
     * Index Daily
     */
    public function indexDailyAerplus(Request $request)
    {
        $year = date('Y');

        if ($request->query('year') !== null) {
            $year = $request->query('year');
        }

        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $dailySalaries = DailySalary::where('type', 'aerplus')
            // ->limit(10)
            ->select(['id', 'start_date', 'end_date'])
            ->whereYear('end_date', $year)
            ->get()
            ->groupBy([function ($salary) {
                return (int) date('m', strtotime($salary->end_date)) - 1;
            }, function ($salary) {
                return $salary->start_date . '/' . $salary->end_date;
            }])->sort()
            ->all();

        return view('payrolls.daily.index-aerplus', [
            'payslips' => $dailySalaries,
            'months' => $months,
            'year' => $year,
        ]);
    }

    /**
     * Show by period
     */
    public function showByPeriodDaily(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($startDate == null || $endDate == null) {
            abort(404);
        }

        $dailySalaries = DailySalary::with(['employee.activeCareer.jobTitle'])
            ->where('type', 'regular')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get();

        return view('payrolls.daily.show-by-period', [
            'salaries' => $dailySalaries,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Show by period
     */
    public function showByPeriodDailyAerplus(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($startDate == null || $endDate == null) {
            abort(404);
        }

        $dailySalaries = DailySalary::with(['employee' => function ($q) {
            $q->with(['activeCareer' => function ($q2) {
                $q2->with(['jobTitle']);
            }, 'office']);
        }])
            ->where('type', 'aerplus')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get();

        $totalTakeHomePay = collect($dailySalaries)->sum('take_home_pay');

        $paid = !empty(collect($dailySalaries)->where('paid', 1)->first()) ? true : false;
        // "date": "2023-06-06",
        // "account_id": "5",
        // "outlet_id": "2",
        // "to": "wqe",
        // "received_by": "asdasd",
        // "description": "",
        // "outletSpendingList": [{
        //     "partnerId": "0",
        //     "tenantId": "0",
        //     "monthlyCommission": "",
        //     "termOfPaymentTenantId": "",
        //     "supplierId": "0",
        //     "expenditureTypeId": 9,
        //     "transactionType": "transfer",
        //     "amount": "1000000",
        //     "npwp": "",
        //     "percentage": "",
        //     "description": "Gaji depot ... periode ...",
        //     "hideDelete": true,
        //     "disabledExpenditureType": false
        // }],
        // "total": 1000000
        // return $dailySalaries;
        $offices = Office::all();
        $outletSpendingPayloads = collect($dailySalaries)->groupBy(function ($dailySalary) {
            return $dailySalary->employee->office->id ?? 0;
        })->map(function ($dailySalaries, $officeId) use ($offices, $startDate, $endDate) {
            $totalOutletTakeHomePay = collect($dailySalaries)->sum('take_home_pay');
            $totalIncomes = collect($dailySalaries)->sum('total_incomes');

            $totalAllRedeemDeposit = 0;
            $totalAllDeposit = 0;
            $totalAllLateFee = 0;
            $totalAllLoan = 0;

            collect($dailySalaries)->each(function ($dailySalary) use (&$totalAllDeposit, &$totalAllRedeemDeposit, &$totalAllLateFee, &$totalAllLoan) {
                $additionalIncomes = json_decode($dailySalary->additional_incomes ?? "[]");
                $deductions = json_decode($dailySalary->deductions ?? "[]");

                $totalRedeemDeposit = collect($additionalIncomes)->where('type', 'redeem_deposit')->sum('value');

                $totalDeposit = collect($deductions)->where('type', 'deposit')->sum('value');
                $totalLateFee = collect($deductions)->where('type', 'late_fee')->sum('value');
                $totalLoan = collect($deductions)->where('type', 'loan')->sum('value');
                // return $totalDeposit;
                // $dailySalary->total_deposit = $totalDeposit;
                // $dailySalary->total_redeem_deposit = $totalRedeemDeposit;
                $totalAllDeposit += $totalDeposit;
                $totalAllRedeemDeposit += $totalRedeemDeposit;
                $totalAllLateFee += $totalLateFee;
                $totalAllLoan += $totalLoan;
            });

            $officeName = collect($offices)->where('id', $officeId)->first()->name ?? '';
            $description = "Gaji depot " . $officeName . " periode " . Carbon::parse($startDate)->isoFormat('LL') . ' - ' . Carbon::parse($endDate)->isoFormat('LL');

            $additionalCredit = [];
            $additionalDebit = [];

            $finalAmount = 0;

            // $hutangDepositKaryawan = $totalAllDeposit - $totalAllRedeemDeposit;
            if ($totalAllRedeemDeposit > 0) {
                $additionalDebit[] = [
                    'amount' => $totalAllRedeemDeposit,
                    'account_id' => 128,
                ];
            }

            if ($totalAllLateFee > 0) {
                $additionalCredit[] = [
                    'amount' => $totalAllLateFee,
                    'account_id' => 197,
                ];
            }

            if ($totalAllDeposit > 0) {
                $additionalCredit[] = [
                    'amount' => $totalAllDeposit,
                    'account_id' => 128,
                ];
            }

            if ($totalAllLoan > 0) {
                $additionalCredit[] = [
                    'amount' => $totalAllLoan,
                    'account_id' => 79,
                ];
            }


            // if ($totalAllRedeemDeposit > 0) {
            // }

            // if ($totalAllDeposit > 0) {

            // }

            // Beban Gaji
            $sofAmount = $totalOutletTakeHomePay;
            $expenseAmount = $totalIncomes - $totalAllRedeemDeposit;
            $journalAmount = $totalOutletTakeHomePay + $totalAllDeposit + $totalAllLateFee + $totalAllLoan;


            // $accountTransaction1->date = $date;
            // $accountTransaction1->description = 'PENGELUARAN PUSAT (HO)' . ' - '  . $expenseList['description'];
            // $accountTransaction1->amount_type = 'CREDIT';
            // $accountTransaction1->amount = $expenseList['amount'];
            // $accountTransaction1->account_id = $sof;
            // $accountTransaction1->journal_id = $journal->id;

            return [
                'date' => $endDate,
                "account_id" => 5,
                "outlet_id" => $officeId,
                "to" => "",
                "received_by" => "",
                "description" => $description,
                "outletSpendingList" => [[
                    "partnerId" => "0",
                    "tenantId" => "0",
                    "monthlyCommission" => "",
                    "termOfPaymentTenantId" => "",
                    "supplierId" => "0",
                    "expenditureTypeId" => 9,
                    "transactionType" => "transfer",
                    "amount" => $totalOutletTakeHomePay,
                    "sof_amount" => $sofAmount,
                    "expense_amount" => $expenseAmount,
                    "journal_amount" => $journalAmount,
                    "npwp" => "",
                    "percentage" => "",
                    "description" => $description,
                    "hideDelete" => true,
                    "disabledExpenditureType" => false,
                    "additional_debit" => $additionalDebit,
                    "additional_credit" => $additionalCredit,
                ]],
                "total" => $totalOutletTakeHomePay,
                // 'total_deposit' => $totalAllDeposit,
                // 'total_redeem_deposit' => $totalAllRedeemDeposit,
            ];
        })->values()->all();

        // $outletSpendingPayloads2 = collect($dailySalaries)->groupBy(function ($dailySalary) {
        //     return $dailySalary->employee->office->id ?? 0;
        // })->map(function ($dailySalaries, $officeId) use ($offices, $startDate, $endDate) {
        //     $totalOutletTakeHomePay = collect($dailySalaries)->sum('take_home_pay');
        //     $officeName = collect($offices)->where('id', $officeId)->first()->name ?? '';
        //     $description = "Gaji depot " . $officeName . " periode " . Carbon::parse($startDate)->isoFormat('LL') . ' - ' . Carbon::parse($endDate)->isoFormat('LL');
        //     return [
        //         "depot" => $officeName,
        //         'formatted_total' => number_format($totalOutletTakeHomePay, 0, '.', ','),
        //         "total" => $totalOutletTakeHomePay,
        //     ];
        // })->values()->all();

        // return [
        //     'distribusi' => $outletSpendingPayloads2,
        //     'total' => number_format(collect($outletSpendingPayloads2)->sum('total'), 0, '.', ','),
        // ];
        // return $outletSpendingPayloads;

        return view('payrolls.daily.show-by-period-aerplus', [
            'salaries' => $dailySalaries,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_thp' => $totalTakeHomePay,
            'outlet_spending_payloads' => $outletSpendingPayloads,
            'paid' => $paid,
        ]);
    }

    /**
     * Show by period
     */
    public function showByPeriodDailyAerplusV2(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($startDate == null || $endDate == null) {
            abort(404);
        }

        $dailySalaries = DailySalary::with(['employee' => function ($q) {
            $q->with(['activeCareer' => function ($q2) {
                $q2->with(['jobTitle']);
            }, 'office']);
        }])
            ->where('type', 'aerplus')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get();

        $totalTakeHomePay = collect($dailySalaries)->sum('take_home_pay');

        $paid = !empty(collect($dailySalaries)->where('paid', 1)->first()) ? true : false;

        $offices = Office::all();

        $dailyWagesByOfficeId = collect($dailySalaries)
            ->flatMap(function ($dailySalary) {
                return json_decode($dailySalary->incomes);
            })
            ->groupBy(function ($dailySalaryIncome) {
                return $dailySalaryIncome->office_id;
            })
            ->map(function ($dailySalaryIncomes) {
                return [
                    'daily' => collect($dailySalaryIncomes)->sum('total')
                ];
            })
            ->all();

        $additionalSalaryComponentsByOfficeId = collect($dailySalaries)->groupBy(function ($dailySalary) {
            // return $dailySalary->employee->office->id ?? 0;
            return $dailySalary->office_id ?? 0;
        })->map(function ($dailySalaries) {
            $totalAllRedeemDeposit = 0;
            $totalAllDeposit = 0;
            $totalAllLateFee = 0;
            $totalAllLoan = 0;

            collect($dailySalaries)->each(function ($dailySalary) use (&$totalAllDeposit, &$totalAllRedeemDeposit, &$totalAllLateFee, &$totalAllLoan) {
                $additionalIncomes = json_decode($dailySalary->additional_incomes ?? "[]");
                $deductions = json_decode($dailySalary->deductions ?? "[]");

                $totalRedeemDeposit = collect($additionalIncomes)->where('type', 'redeem_deposit')->sum('value');

                $totalDeposit = collect($deductions)->where('type', 'deposit')->sum('value');
                $totalLateFee = collect($deductions)->where('type', 'late_fee')->sum('value');
                $totalLoan = collect($deductions)->where('type', 'loan')->sum('value');

                $totalAllDeposit += $totalDeposit;
                $totalAllRedeemDeposit += $totalRedeemDeposit;
                $totalAllLateFee += $totalLateFee;
                $totalAllLoan += $totalLoan;
            });

            return [
                'total_all_deposit' => $totalAllDeposit,
                'total_all_redeem_deposit' => $totalAllRedeemDeposit,
                'total_all_late_fee' => $totalAllLateFee,
                'total_all_loan' => $totalAllLoan,
            ];
        })->all();

        $dailyWagesByOfficeIdKeys = collect($dailyWagesByOfficeId)->keys()->all();
        $additionalSalaryComponentsByOfficeIdKeys = collect($additionalSalaryComponentsByOfficeId)->keys()->all();

        $dailySalaryOfficeIds = collect($dailyWagesByOfficeIdKeys)->merge($additionalSalaryComponentsByOfficeIdKeys)->unique()->all();
        $dailySalaryOffices = collect($offices)->whereIn('id', $dailySalaryOfficeIds)->all();

        $officesById = collect($offices)->mapWithKeys(function ($office) {
            return [
                $office->id => $office->name
            ];
        })->all();
        // return $officesById;

        $outletSpendingPayloads = collect($dailySalaryOffices)->map(function ($office) use ($dailyWagesByOfficeId, $additionalSalaryComponentsByOfficeId, $startDate, $endDate, $officesById) {
            $totalDaily = $dailyWagesByOfficeId[$office->id]['daily'] ?? 0;
            $totalDeposit = $additionalSalaryComponentsByOfficeId[$office->id]['total_all_deposit'] ?? 0;
            $totalRedeemDeposit = $additionalSalaryComponentsByOfficeId[$office->id]['total_all_redeem_deposit'] ?? 0;
            $totalLateFee = $additionalSalaryComponentsByOfficeId[$office->id]['total_all_late_fee'] ?? 0;
            $totalLoan = $additionalSalaryComponentsByOfficeId[$office->id]['total_all_loan'] ?? 0;

            $officeName = $officesById[$office->id] ?? '';
            $description = "Gaji depot " . $officeName . " periode " . Carbon::parse($startDate)->isoFormat('LL') . ' - ' . Carbon::parse($endDate)->isoFormat('LL');

            $additionalCredit = [];
            $additionalDebit = [];

            $finalAmount = 0;

            // $hutangDepositKaryawan = $totalAllDeposit - $totalAllRedeemDeposit;
            if ($totalRedeemDeposit > 0) {
                $additionalDebit[] = [
                    'amount' => $totalRedeemDeposit,
                    'account_id' => 128,
                ];
            }

            if ($totalLateFee > 0) {
                $additionalCredit[] = [
                    'amount' => $totalLateFee,
                    'account_id' => 197,
                ];
            }

            if ($totalDeposit > 0) {
                $additionalCredit[] = [
                    'amount' => $totalDeposit,
                    'account_id' => 128,
                ];
            }

            if ($totalLoan > 0) {
                $additionalCredit[] = [
                    'amount' => $totalLoan,
                    'account_id' => 79,
                ];
            }

            // Beban Gaji
            $totalOutletTakeHomePay = $totalDaily - ($totalLoan + $totalLateFee);
            $sofAmount = $totalOutletTakeHomePay;
            $expenseAmount = $totalDaily - $totalRedeemDeposit;
            $journalAmount = $totalDaily + $totalDeposit + $totalLateFee + $totalLoan;


            // $accountTransaction1->date = $date;
            // $accountTransaction1->description = 'PENGELUARAN PUSAT (HO)' . ' - '  . $expenseList['description'];
            // $accountTransaction1->amount_type = 'CREDIT';
            // $accountTransaction1->amount = $expenseList['amount'];
            // $accountTransaction1->account_id = $sof;
            // $accountTransaction1->journal_id = $journal->id;

            return [
                'date' => $endDate,
                "account_id" => 5,
                "outlet_id" => $office->id,
                "to" => "",
                "received_by" => "",
                "description" => $description,
                "outletSpendingList" => [[
                    "partnerId" => "0",
                    "tenantId" => "0",
                    "monthlyCommission" => "",
                    "termOfPaymentTenantId" => "",
                    "supplierId" => "0",
                    "expenditureTypeId" => 9,
                    "transactionType" => "transfer",
                    "amount" => $totalOutletTakeHomePay,
                    "sof_amount" => $sofAmount,
                    "expense_amount" => $expenseAmount,
                    "journal_amount" => $journalAmount,
                    "npwp" => "",
                    "percentage" => "",
                    "description" => $description,
                    "hideDelete" => true,
                    "disabledExpenditureType" => false,
                    "additional_debit" => $additionalDebit,
                    "additional_credit" => $additionalCredit,
                ]],
                "total" => $totalOutletTakeHomePay,
                // 'total_deposit' => $totalAllDeposit,
                // 'total_redeem_deposit' => $totalAllRedeemDeposit,
            ];

            // return [
            //     'office_id' => $office->id,
            //     'total_daily' => $totalDaily,
            //     'total_deposit' => $totalDeposit,
            //     'total_redeem_deposit' => $totalRedeemDeposit,
            //     'total_late_fee' => $totalLateFee,
            //     'total_loan' => $totalLoan,
            // ];
        })->all();

        // return $outletSpendingPayloads;

        return view('payrolls.daily.show-by-period-aerplus', [
            'salaries' => $dailySalaries,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_thp' => $totalTakeHomePay,
            'outlet_spending_payloads' => $outletSpendingPayloads,
            'paid' => $paid,
        ]);
    }

    /**
     * Generate Daily
     */
    public function generateDaily()
    {
        $companies = Company::all();
        return view('payrolls.daily.generate', [
            'companies' => $companies,
        ]);
    }

    /**
     * Generate Daily Aerplus
     */
    public function generateDailyAerplus()
    {
        $companies = Company::all();
        return view('payrolls.daily.generate-aerplus-v2', [
            'companies' => $companies,
        ]);
    }

    /**
     * Generate Daily
     */
    public function monthlyPayment(Request $request)
    {
        // Generated
        $currentDate = date('Y-m-d');
        $salaryStartDayPeriod = 26;
        $salaryStartMonthPeriod = $request->query('month') ?? date('m');
        $salaryStartYearPeriod = $request->query('year') ?? date('Y');
        $companyId = $request->query('company_id');
        $staffOnly = $request->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;
        $AERPLUS_ID = 6;

        if ($salaryStartMonthPeriod == null || $salaryStartYearPeriod == null) {
            return response()->json([
                'message' => 'Bulan dan tahun diperlukan',
            ], 400);
        }

        $carbonDate = Carbon::parse($salaryStartYearPeriod . '-' . $salaryStartMonthPeriod . '-' . $salaryStartDayPeriod);

        // $startDate = $carbonDate->subMonth()->toDateString();
        // $endDate = $carbonDate->addMonth()->subDay()->toDateString();
        $startDate = request()->query('start_date_period');
        $endDate = request()->query('end_date_period');

        // $staffOnly = false;
        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $userGroup = auth()->user()->group ?? null;
        $haveAllCompanyPermissions = $userGroup->have_all_company_permissions ?? false;
        $companyPermissions = json_decode($userGroup->company_permissions ?? "[]");

        if ($haveAllCompanyPermissions == false && count($companyPermissions) < 1) {
            return response('<span style="font-family:courier, courier new, serif;">ERROR 403 FORBIDDEN: YOU DONT HAVE ACCESS TO ANY COMPANIES</span>', 200)
                ->header('Content-Type', 'text/html');
            // return view();
        }

        $companies = [];
        if ($haveAllCompanyPermissions == true) {
            $companies = Company::all();
        } else {
            $companies = Company::whereIn('id', $companyPermissions)->get();
        }

        if (!isset($companyId) || $companyId == "") {
            $companyId = collect($companies)->first()->id ?? null;
        }

        // return $companyId;

        // return response()->json([
        //     'have_staff_permission' => $haveStaffPermission,
        //     'staff_only' => $staffOnly,
        // ]);
        // return $haveStaffPermission;

        $employeesHasSalariesCollection = Employee::whereHas('salaries', function (Builder $q) use ($startDate, $endDate) {
            return $q->where('start_date', $startDate)->where('end_date', $endDate);
        })->with(['salaries' => function ($q) use ($startDate, $endDate) {
            return $q->where('start_date', $startDate)->where('end_date', $endDate);
        }]);

        if ($companyId == $AERPLUS_ID) {
            $employeesHasSalariesCollection->where('type', 'staff');
        } else {
            if (!$haveStaffPermission) {
                $employeesHasSalariesCollection->where('type', '!=', 'staff');
            } else {
                if ($staffOnly) {
                    $employeesHasSalariesCollection->where('type', 'staff');
                }
            }
        }

        if (isset($companyId) && $companyId !== "") {
            // return $companyId;
            $employeesHasSalariesCollection->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        // return 'no company id';

        $employeesHasSalariesCollection = $employeesHasSalariesCollection
            ->get()
            ->filter(function ($employee) {
                return count($employee->salaries) > 0;
            });

        // ? FIX SALARIES -----------------
        $fixSalaries = $employeesHasSalariesCollection->map(function ($employee) {
            $salaryItems = [];
            $salary = null;
            if (isset($employee->salaries[0])) {
                $salary = $employee->salaries[0];
                $salaryItems = $salary->items;
            }
            $incomes = collect($salaryItems)->where('type', 'income')->all();
            $deductions = collect($salaryItems)->where('type', 'deduction')->all();

            $totalIncomes = collect($incomes)->sum('amount');
            $totalDeductions = collect($deductions)->sum('amount');
            $totalTakeHomePay = $totalIncomes - $totalDeductions;

            return [
                'salary' => $salary,
                'employee' => $employee,
                'incomes' => $incomes,
                'deductions' => $deductions,
                'summary' => [
                    'total_incomes' => $totalIncomes,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $totalTakeHomePay,
                ],
            ];
        })->all();

        $employeesHasSalariesIds = $employeesHasSalariesCollection->pluck('id')->all();
        // Summary
        $totalTakeHomePaySalaries = collect($fixSalaries)->sum(function ($salary) {
            return $salary['summary']['take_home_pay'];
        });

        // -------------------------

        $massLeaves = EventCalendar::query()->where('type', 'cuti_bersama')->whereYear('date', date('Y'))->get();
        // $leaveQuota = 12;

        $employees = Employee::with([
            'attendances' => function ($q) {
                $q->orderBy('id', 'desc');
            },
            'activeWorkingPatterns.items',
            'loans.items',
            // 'salaryComponents',
            'salaryComponents' => function ($q) use ($currentDate) {
                $q->wherePivot('effective_date', '<=', $currentDate)->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
            },
            'leave',
            'activeCareer.jobTitle',
            'leaveApplications' => function ($q) {
                $q->where('approval_status', 'approved');
            }
        ]);

        if (!$haveStaffPermission) {
            $employees->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $employees->where('type', 'staff');
            }
        }

        if (isset($companyId) && $companyId !== "") {
            // return $companyId;
            $employees->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        $employees = $employees
            ->where(
                'active',
                1
            )
            ->whereNotIn('id', $employeesHasSalariesIds)
            ->get();

        // return $employees;

        $eventCalendars = EventCalendar::all();

        $startDateAttendancePeriod = $startDate;
        $endDateAttendancePeriod = $endDate;
        if ($startDate == '2023-03-26' && $endDate == '2023-04-25') {
            $endDateAttendancePeriod = '2023-04-15';
        }
        $periodDates = $this->getDatesFromRange($startDateAttendancePeriod, $endDateAttendancePeriod);

        // ? GENERATED SALARIES --------------
        $generatedSalaries = [];

        // return response()->json(collect($generatedSalaries)->filter(function ($salary) {
        //     return $salary['employee']['id'] == 44;
        // })->first());

        // Generated
        $totalTakeHomePayGeneratedSalaries = collect($generatedSalaries)->sum(function ($salary) {
            return $salary['summary']['take_home_pay'];
        });

        // ?SALARIES SUMMARY (REKAP)
        $salariesSummaryEmployees = Employee::query()->whereHas('salaries', function ($query) use ($startDate, $endDate) {
            $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
        })
            ->with([
                'activeCareer.jobTitle',
                'office.division.company',
                'bankAccounts',
                'loans' => function ($q) use ($endDate) {
                    $q->with(['items' => function ($itemQuery) use ($endDate) {
                        $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
                    }]);
                },
                'salaries' => function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', $startDate)->where('end_date', $endDate);
                }
            ]);

        if (!$haveStaffPermission) {
            $salariesSummaryEmployees->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $salariesSummaryEmployees->where('type', 'staff');
            }
        }

        $salariesSummaryEmployees = $salariesSummaryEmployees->get()
            ->map(function ($employee) use ($startDate, $endDate) {
                $basicSalary = 0;
                $positionAllowance = 0;
                $attendanceAllowance = 0;
                $currentMonthLoans = 0;
                $excessLeave = 0;
                $total = 0;
                $bankAccountNumber = null;

                $defaultBankAccount = collect($employee->bankAccounts)->where('default', 1)->first();
                if ($defaultBankAccount !== null) {
                    $bankAccountNumber = $defaultBankAccount['account_number'];
                }

                $totalLoan = collect($employee->loans)->sum('amount');
                // $totalPaidLoan = collect($employee->loans)->pluck('items')->filter(function ($item) {
                //     return $item->salary_item_count > 0;
                // })->sum('amount');
                $totalPaidLoan = collect($employee->loans)->flatMap(function ($loan) {
                    return $loan->items;
                })->filter(function ($item) {
                    return $item->salary_item_count > 0;
                })->sum('basic_payment');

                $remainingLoan = $totalLoan - $totalPaidLoan;

                $currentMonthSingleLoan = collect($employee->loans)->whereBetween('effective_date', [$startDate, $endDate])->sum('amount');

                $salariesDataCount = collect($employee->salaries)->count();
                // if ($salariesDataCount > 0) {
                //     $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                //         return $salary->items;
                //     });
                //     $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                //     $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                //     $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                //     $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
                //     $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                //     $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                // }
                if ($salariesDataCount > 0) {
                    $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                        return $salary->items;
                    });
                    $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                    $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                    $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                    $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->where('type', 'deduction')->sum('amount');
                    $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                    // $total = ($basicSalary + $positionAllowance + $attendanceAllowance + $currentMonthSingleLoan) - ($currentMonthLoans + $excessLeave);
                    $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                }

                // Gaji Pokok
                $employee->basic_salary = $basicSalary;
                // Tunjangan
                $employee->position_allowance = $positionAllowance;
                // Insentif Kehadiran
                $employee->attendance_allowance = $attendanceAllowance;
                // Bruto
                $employee->bruto_salary = $basicSalary + $positionAllowance + $attendanceAllowance;
                // Piutang Awal
                $employee->total_loan = $totalLoan;
                // Piutang Pinjaman
                $employee->current_month_loan = $currentMonthSingleLoan;
                // Piutang Potongan
                $employee->loan = $currentMonthLoans;
                // Piutang Sisa
                $employee->remaining_loan = $remainingLoan;
                // Total Paid Loan
                $employee->total_paid_loan = $totalPaidLoan;
                // Kelebihan Cuti
                $employee->excess_leave = $excessLeave;
                // Netto
                $employee->total = $total;
                // Bank Account
                $employee->bank_account_number = $bankAccountNumber;

                return $employee;
            })->all();

        // return $salariesSummaryEmployees;
        $company = Company::find($companyId);

        return view('payrolls.monthly.payment', [
            'company' => $company,
            'summary_salaries' => [
                'salaries' => $salariesSummaryEmployees,
            ],
            'fix_salaries' => [
                'salaries' => $fixSalaries,
                'total_employees' => count($fixSalaries),
                'total_take_home_pay' => $totalTakeHomePaySalaries,
            ],
            'generated_salaries' => [
                'salaries' => $generatedSalaries,
                'total_employees' => count($generatedSalaries),
                'total_take_home_pay' => $totalTakeHomePayGeneratedSalaries,
            ],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'salary_month' => $salaryStartMonthPeriod,
            'salary_year' => $salaryStartYearPeriod,
            'staff_only' => $staffOnly,
            'company_id' => $companyId,
            'have_staff_permission' => $haveStaffPermission,
            'companies' => $companies,
        ]);
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

    public function updateMagentaDailySalarySetting(Request $request)
    {
        DB::beginTransaction();
        try {
            $setting = MagentaDailySalarySetting::first();

            if ($setting !== null) {
                $setting->update([
                    'report_receiver' => $request->report_receiver,
                    'report_maker' => $request->report_maker,
                    'updated_by' => Auth::user()->employee->id ?? null,
                ]);
            } else {
                $setting = MagentaDailySalarySetting::create([
                    'report_receiver' => $request->report_receiver,
                    'report_maker' => $request->report_maker,
                    'updated_by' => Auth::user()->employee->id ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Pengaturan berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
