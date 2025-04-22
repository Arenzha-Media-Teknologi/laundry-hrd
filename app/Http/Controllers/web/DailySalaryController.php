<?php

namespace App\Http\Controllers\web;

use App\Exports\dailysalaries\AerplusDailySalaryExport;
use App\Exports\dailysalaries\AerplusDailySalarySummaryExport;
use App\Exports\dailysalaries\MagentaDailySalaryExport;
use App\Http\Controllers\Controller;
use App\Models\DailySalary;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\LoanItem;
use App\Models\MagentaDailySalarySetting;
use App\Models\Office;
use App\Models\SalaryComponent;
use App\Models\SalaryDeposit;
use App\Models\SalaryDepositItem;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class DailySalaryController extends Controller
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
        $salary = DailySalary::with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'office.division.company']);
        }])->findOrFail($id);
        $salaryComponents = SalaryComponent::all();
        return view('daily-salaries.aerplus-edit', [
            'salary' => $salary,
            'salary_components' => $salaryComponents,
        ]);
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
        try {
            $salary = DailySalary::findOrFail($id);
            $salary->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the bulk resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();
        try {

            $ids = json_decode($request->query('ids'));
            $dailySalaries = DailySalary::query()->whereIn('id', $ids)->get();

            $allDepositIds = [];
            $allDepositItemIds = [];
            $allLoanItemIds = [];

            collect($dailySalaries)->each(function ($dailySalary) use (&$allDepositIds, &$allDepositItemIds, &$allLoanItemIds) {
                $additionalIncomes = json_decode($dailySalary->additional_incomes ?? "[]", true);
                $depositIds = collect($additionalIncomes)->where('type', 'redeem_deposit')
                    ->flatMap(function ($deposit) {
                        return $deposit['id'] ?? 0;
                    })->all();
                $allDepositIds[] = $depositIds;

                $deductions = json_decode($dailySalary->deductions ?? "[]", true);
                $depositItemIds = collect($deductions)->where('type', 'deposit')
                    ->flatMap(function ($deposit) {
                        return $deposit['id'] ?? 0;
                    })->all();
                $allDepositItemIds[] = $depositItemIds;

                //! OLD
                // $loanItemIds = collect($deductions)->where('type', 'loan')
                //     ->flatMap(function ($loanItem) {
                //         return $loanItem['id'] ?? 0;
                //     })->all();
                //* NEW
                $loanItemIds = collect($deductions)->where('type', 'loan')
                    ->map(function ($loanItem) {
                        return $loanItem['id'] ?? 0;
                    })->all();
                $allLoanItemIds[] = $loanItemIds;
            });

            SalaryDeposit::whereIn('id', collect($allDepositIds)->flatten()->all())
                ->update([
                    'redeemed' => 0,
                    'redeemed_amount' => null,
                    'redeemed_date' => null,
                ]);

            SalaryDepositItem::whereIn('id', collect($allDepositItemIds)->flatten()->all())
                ->update([
                    'paid' => 0,
                    'paid_date' => null,
                ]);

            LoanItem::whereIn('id', collect($allLoanItemIds)->flatten()->all())
                ->update([
                    'paid' => 0,
                ]);

            $paymentBatchCodes = $dailySalaries->pluck('payment_batch_code')->all();
            $paymentBatchCodes = array_unique($paymentBatchCodes);

            DailySalary::query()->whereIn('id', $ids)->delete();

            // $response = Http::delete('http://aerplus-central.test/api/v1/outlet-spendings/delete-from-payment-batch-code', [
            //     'payment_batch_code' => json_encode($paymentBatchCodes),
            // ]);

            DB::commit();

            return response()->json([
                'message' => count($ids) . ' Data berhasil dihapus',
                'data' => [
                    // 'reponse' => $response,
                    'payment_batch_codes' => $paymentBatchCodes,
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate daily salaries
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
            $employees = Employee::with(['attendances' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
            }, 'activeWorkingPatterns' => function ($q) {
                $q->with(['items']);
            }, 'activeCareer.jobTitle', 'salaryComponents'])->where('magenta_daily_salary', 1)->where('active', 1)->orderBy('name')->get();

            // return $employees;

            $eventCalendars = EventCalendar::all();

            $periodDates = $this->getDatesFromRange($startDate, $endDate);

            // Number of days
            $numberOfDays = Carbon::parse($startDate)->diffInDays($endDate);

            $salaries = collect($employees)->map(function ($employee) use ($periodDates, $eventCalendars, $numberOfDays) {
                $attendances = $employee->attendances;
                $workingPatterns = $employee->activeWorkingPatterns;
                $activeWorkingPattern = collect($workingPatterns)->first();
                $salaryComponents = $employee->salaryComponents;

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

                $totalTimeLate = 0;
                // $dailyWage = 50000;
                // $dailyWageCoefficient = 2;
                // $overtimePay = 5000;
                // $overtimePayCoefficient = 2;

                $periods = collect($periodDates)->map(function ($date) use ($attendances, $activeWorkingPattern, $eventCalendars, $employeeWage, &$totalTimeLate) {
                    // Resource
                    $newestAttendance = collect($attendances)->where('date', $date)->first();
                    $currentEvents = collect($eventCalendars)->filter(function ($event) use ($date) {
                        return $event['date'] == $date && ($event['type'] == 'libur_nasional' || $event['type'] == 'cuti_bersama');
                    })->values()->all();

                    $eventCalendarsExist = count($currentEvents) > 0 ? true : false;

                    $dayOrder = Carbon::parse($date)->dayOfWeekIso;

                    $dailyWage = $employeeWage['daily'];
                    $overtimePay = 0;

                    $workingPatternItems = [];
                    if (isset($activeWorkingPattern['items'])) {
                        $workingPatternItems = $activeWorkingPattern['items'];
                    }
                    $workingPatternDay = collect($workingPatternItems)->where('order', $dayOrder)->first();

                    // Attendance attributes
                    $attendance = [
                        'status' => null,
                        'working_pattern_day' => null,
                        // 'event_calendars' => $currentEvents,
                        'clock_in_time' => null,
                        'clock_out_time' => null,
                        'overtime' => 0,
                        'time_late' => 0,
                    ];

                    if ($workingPatternDay !== null) {
                        if (isset($workingPatternDay['day_status'])) {
                            $attendance['working_pattern_day'] = $workingPatternDay['day_status'];
                        }
                    }

                    if ($newestAttendance == null) {
                        $dailyWage = 0;
                        $isHoliday = false;

                        if ($workingPatternDay !== null) {
                            if ($workingPatternDay['day_status'] == 'holiday') {
                                $isHoliday = true;
                            }
                        }

                        if ($eventCalendarsExist && $isHoliday == false) {
                            $dailyWage = $employeeWage['daily'];
                        }
                    } else {
                        // Attendance attributes
                        $attendance['status'] = $newestAttendance['status'];
                        if ($newestAttendance['status'] == 'hadir') {
                            $attendance['clock_in_time'] = $newestAttendance['clock_in_time'];
                            $attendance['clock_out_time'] = $newestAttendance['clock_out_time'];
                            // !APPLICATION OVERTIME
                            $attendance['overtime'] = $newestAttendance['overtime'];
                            if ($date > "2024-09-26") {
                                $attendance['overtime'] = $newestAttendance['application_overtime'];
                            }
                            // !APPLICATION OVERTIME
                            $attendance['overtime_hours'] = 0;
                        }
                        // End: Attendance attributes

                        if ($activeWorkingPattern !== null) {
                            $dailyWage = $employeeWage['daily'];
                            // $dailyWage = 0;
                            $isTimeOff = $newestAttendance['status'] == 'sakit' || $newestAttendance['status'] == 'cuti' || $newestAttendance['status'] == 'izin';

                            if ($newestAttendance['status'] == 'hadir') {

                                // !APPLICATION OVERTIME
                                $newestAttendanceOvertime = $newestAttendance['overtime'] ?? 0;
                                if ($date > "2024-09-26") {
                                    $newestAttendanceOvertime = $newestAttendance['application_overtime'] ?? 0;
                                }
                                // !APPLICATION OVERTIME
                                $overtimeHours = 0;

                                $x = $newestAttendanceOvertime % 30; // 0
                                $y = ($newestAttendanceOvertime - $x) / 30; // 3 

                                if ($y > 0) {
                                    $z = ($newestAttendanceOvertime - $x) - 30; // (90 - 0) - 30 = 60
                                    $overtimeHours = 1 + floor($z / 60); // 1 + floor(60 / 60) = 2
                                }

                                $overtimeHours = ($overtimeHours > 0) ? $overtimeHours : 0;
                                $attendance['overtime'] = $overtimeHours;
                                $attendance['overtime_hours'] = $overtimeHours;

                                // $overtimePay = $employeeWage['overtime'] * ($newestAttendance['overtime'] / 60);
                                $overtimePay = $employeeWage['overtime'] * $overtimeHours;
                                $isApplyCoefficient = false;
                                $timeLate = $newestAttendance['time_late'];

                                if ($workingPatternDay !== null) {
                                    // When status is holiday
                                    if ($workingPatternDay['day_status'] == 'holiday') {
                                        $isApplyCoefficient = true;
                                    }
                                }

                                // When event calendar exist
                                if ($eventCalendarsExist) {
                                    $isApplyCoefficient = true;
                                }

                                if ($isApplyCoefficient) {
                                    $dailyWage = $dailyWage * $employeeWage['daily_coefficient'];
                                    $overtimePay = $overtimePay * $employeeWage['overtime_coefficient'];
                                }

                                // Time late
                                if ($workingPatternDay['day_status'] == 'holiday' || $eventCalendarsExist) {
                                    $timeLate = 0;
                                }

                                $attendance['time_late'] = $timeLate;

                                $totalTimeLate += $timeLate;
                            } else if ($isTimeOff) {
                                $isHoliday = false;

                                if ($workingPatternDay !== null) {
                                    if ($workingPatternDay['day_status'] == 'holiday') {
                                        $isHoliday = true;
                                    }
                                }

                                if ($isHoliday) {
                                    $dailyWage = 0;
                                }
                            } else {
                                $dailyWage = 0;
                            }
                        }
                    }

                    return [
                        'date' => $date,
                        // 'attendance' => $newestAttendance,
                        'day_name' => Carbon::parse($date)->locale('id')->dayName,
                        'editing_daily_wage' => false,
                        'daily_wage' => $dailyWage,
                        // 'new_daily_wage' => $dailyWage,
                        'editing_overtime_pay' => false,
                        'overtime_pay' => round($overtimePay),
                        // 'new_overtime_pay' => round($overtimePay),
                        'total' => $dailyWage + round($overtimePay),
                        'attendance' => $attendance,
                        'event_calendars' => $currentEvents,
                        // 'working_pattern_day' => $workingPatternDay,
                    ];
                })->all();

                $lateCharge = 0;
                if ($totalTimeLate > 0 && $totalTimeLate <= 60) {
                    $lateCharge = $employeeWage['daily'] * (50 / 100);
                } else if ($totalTimeLate > 60) {
                    $lateCharge = $employeeWage['daily'];
                }

                // Summary
                $totalIncomes = collect($periods)->sum('total');
                $totalDeductions = $lateCharge;
                $takeHomePay = $totalIncomes - $totalDeductions;

                return [
                    'employee' => collect($employee)->except('attendances')->all(),
                    'periods' => $periods,
                    'total_time_late' => $totalTimeLate,
                    'late_charge' => $lateCharge,
                    'number_of_days' => $numberOfDays + 1,
                    'have_active_working_pattern' => $activeWorkingPattern !== null ? true : false,
                    'summary' => [
                        'total_incomes' => $totalIncomes,
                        'total_deductions' => $totalDeductions,
                        'take_home_pay' => $takeHomePay,
                    ]
                ];
            })->all();

            // Employees count
            $employeesCount = count($employees);
            $totalTakeHomePay = collect($salaries)->sum(function ($salary) {
                return $salary['summary']['take_home_pay'];
            });

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'salaries' => $salaries,
                    'total_employees' => $employeesCount,
                    'total_take_home_pay' => $totalTakeHomePay,
                ],
            ]);
            // return view('daily-salaries.index', [
            //     'salaries' => $salaries,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate daily salaries for aerplus
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateAerplus(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($startDate == null || $endDate == null) {
            return response()->json([
                'message' => 'Tanggal awal dan tanggal akhir diperlukan',
            ], 400);
        }

        try {
            $employees = Employee::whereDoesntHave('dailySalaries', function ($q) use ($startDate, $endDate) {
                $q->where('start_date', $startDate)->where('end_date', $endDate)->where('type', 'aerplus');
            })->with([
                'attendances' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
                },
                'activeWorkingPatterns' => function ($q) {
                    $q->with(['items']);
                },
                'activeCareer.jobTitle',
                'salaryComponents',
                'salaryDeposits' => function ($q) {
                    $q->with(['items']);
                },
                'loans' => function ($q) use ($startDate, $endDate) {
                    $q->with(['items' => function ($itemQuery) use ($startDate, $endDate) {
                        $itemQuery->where('paid', 0);
                        // ->whereBetween('payment_date', [$startDate, $endDate]);
                    }]);
                }
            ])->where('aerplus_daily_salary', 1)->where('active', 1)->get();

            // return collect($employees[0])->except(['attendances', 'salary_components'])->all();

            $eventCalendars = EventCalendar::all();

            $periodDates = $this->getDatesFromRange($startDate, $endDate);

            // Number of days
            $numberOfDays = Carbon::parse($startDate)->diffInDays($endDate);

            $salaries = collect($employees)->map(function ($employee) use ($periodDates, $eventCalendars, $numberOfDays) {
                $attendances = $employee->attendances;
                $workingPatterns = $employee->activeWorkingPatterns;
                $activeWorkingPattern = collect($workingPatterns)->first();
                // -------
                $salaryComponents = $employee->salaryComponents;

                $dailyWageComponent = collect($salaryComponents)->where('salary_type', 'uang_harian')->first();
                $uangMakanComponent = collect($salaryComponents)->where('salary_type', 'uang_makan')->first();
                $overtimePayComponent = collect($salaryComponents)->where('salary_type', 'lembur')->first();
                $tunjanganHarianComponent = collect($salaryComponents)->where('salary_type', 'tunjangan_harian')->first();

                // !DEVELOPMENT ONLY
                $employeeWage = [
                    'daily' => 0,
                    'daily_coefficient' => 1,
                    'overtime' => 0,
                    'overtime_coefficient' => 1,
                ];

                if ($dailyWageComponent !== null) {
                    // if (isset($dailyWageComponent['pivot']['amount']) && isset($dailyWageComponent['pivot']['coefficient'])) {
                    //     $employeeWage['daily'] = $dailyWageComponent['pivot']['amount'] ;
                    //     $employeeWage['daily_coefficient'] = $dailyWageComponent['pivot']['coefficient'];
                    // }
                    $employeeWage['daily'] = ($dailyWageComponent['pivot']['amount'] ?? 0) + ($uangMakanComponent['pivot']['amount'] ?? 0);
                    $employeeWage['daily_coefficient'] = $dailyWageComponent['pivot']['coefficient'] ?? 0;
                }

                if ($dailyWageComponent !== null) {
                    if (isset($overtimePayComponent['pivot']['amount']) && isset($overtimePayComponent['pivot']['coefficient'])) {
                        $employeeWage['overtime'] = $overtimePayComponent['pivot']['amount'];
                        $employeeWage['overtime_coefficient'] = $overtimePayComponent['pivot']['coefficient'];
                    }
                }

                // !DEVELOPMENT ONLY
                // $employeeWage = [
                //     'daily' => 50000,
                //     'daily_coefficient' => 2,
                //     'overtime' => 5000,
                //     'overtime_coefficient' => 2,
                // ];

                $totalTimeLate = 0;
                // $dailyWage = 50000;
                // $dailyWageCoefficient = 2;
                // $overtimePay = 5000;
                // $overtimePayCoefficient = 2;
                // (Harian / 2) + Harian + Tunjangan

                $periods = collect($periodDates)->map(function ($date) use ($employee, $attendances, $activeWorkingPattern, $eventCalendars, $employeeWage, &$totalTimeLate, $uangMakanComponent, $dailyWageComponent) {
                    // Resource
                    $newestAttendance = collect($attendances)->where('date', $date)->first();
                    $currentEvents = collect($eventCalendars)->filter(function ($event) use ($date) {
                        return $event['date'] == $date && ($event['type'] == 'libur_nasional' || $event['type'] == 'cuti_bersama');
                    })->values()->all();

                    $eventCalendarsExist = count($currentEvents) > 0 ? true : false;

                    $dayOrder = Carbon::parse($date)->dayOfWeekIso;

                    $dailyWage = 0;
                    $overtimePay = 0;

                    $workingPatternItems = [];
                    if (isset($activeWorkingPattern['items'])) {
                        $workingPatternItems = $activeWorkingPattern['items'];
                    }
                    $workingPatternDay = collect($workingPatternItems)->where('order', $dayOrder)->first();

                    // Attendance attributes
                    $attendance = [
                        'status' => null,
                        'working_pattern_day' => null,
                        'event_calendars' => $currentEvents,
                        'clock_in_time' => null,
                        'clock_out_time' => null,
                        'overtime' => 0,
                        'time_late' => 0,
                        'is_long_shift' => false,
                    ];

                    if ($workingPatternDay !== null) {
                        if (isset($workingPatternDay['day_status'])) {
                            $attendance['working_pattern_day'] = $workingPatternDay['day_status'];
                        }
                    }

                    if ($newestAttendance !== null) {
                        $attendance['status'] = $newestAttendance['status'];

                        if ($newestAttendance['status'] == 'hadir') {
                            // Attendance attributes
                            $attendance['clock_in_time'] = $newestAttendance['clock_in_time'];
                            $attendance['clock_out_time'] = $newestAttendance['clock_out_time'];
                            $attendance['overtime'] = $newestAttendance['overtime'];
                            $attendance['is_long_shift'] = $newestAttendance['is_long_shift'] == 1 ? true : false;
                            // End: Attendance attributes

                            $dailyWage = $employeeWage['daily'];

                            //! OVERTIME CHANGES
                            // if ($employee->aerplus_overtime == 1) {
                            //     $overtimePay = $employeeWage['overtime'] * floor($newestAttendance['overtime'] / 60);
                            // }
                            $overtimePay = $employeeWage['overtime'] * floor($newestAttendance['overtime'] / 60);
                            // $overtimePay = 99999;
                            //! OVERTIME CHANGES

                            $isApplyCoefficient = false;
                            $timeLate = $newestAttendance['time_late'];
                            $isLongShift = $newestAttendance['is_long_shift'];

                            // When is long shift
                            if ($isLongShift) {
                                // $isApplyCoefficient = true;
                                $dailyWageComponentAmount = $dailyWageComponent['pivot']['amount'] ?? 0;
                                $uangMakanComponentAmount = $uangMakanComponent['pivot']['amount'] ?? 0;
                                $longShiftAdditionalIncome = ($dailyWageComponentAmount / 2) + $dailyWageComponentAmount + $uangMakanComponentAmount;
                                // $overtimePay = $longShiftAdditionalIncome;
                                $dailyWage = $longShiftAdditionalIncome;
                            }

                            // if ($isApplyCoefficient) {
                            //     $dailyWage = $dailyWage * $employeeWage['daily_coefficient'];
                            // }

                            $attendance['time_late'] = $timeLate;

                            $totalTimeLate += $timeLate;
                        }
                    }

                    return [
                        'date' => $date,
                        'day_name' => Carbon::parse($date)->locale('id')->dayName,
                        // 'attendance' => $newestAttendance,
                        'editing_daily_wage' => false,
                        'daily_wage' => $dailyWage,
                        // 'new_daily_wage' => $dailyWage,
                        'overtime_pay' => round($overtimePay),
                        'editing_overtime_pay' => false,
                        'total' => $dailyWage + round($overtimePay),
                        'attendance' => $attendance,
                    ];
                })->all();

                $lateCharge = 0;
                if ($totalTimeLate > 0 && $totalTimeLate <= 60) {
                    $lateCharge = $employeeWage['daily'] * (50 / 100);
                } else if ($totalTimeLate > 60) {
                    $lateCharge = $employeeWage['daily'];
                }

                $lateCharge = 0;

                /**
                 * Deposit
                 */
                $unpaidDeposit = collect($employee->salaryDeposits)->where('redeemed', 0)->map(function ($salaryDeposit) {
                    $unpaid = collect($salaryDeposit->items)->where('paid', 0)->first();
                    return $unpaid;
                })->filter(function ($salaryDepositItem) {
                    return $salaryDepositItem !== null;
                });

                $totalUnpaidDeposit = $unpaidDeposit->sum('amount');
                $unpaidDepositCount = $unpaidDeposit->count();

                $unredeemedDeposit = collect($employee->salaryDeposits)->filter(function ($salaryDeposit) {
                    // return $salaryDeposit->redeemed == 0 && collect($salaryDeposit->items)->where('paid', 0)->count() < 1;
                    // return true;

                    // return collect($salaryDeposit->items)->where('paid', 0)->count() < 1;
                    return $salaryDeposit->redeemed == 1;
                });

                $totalUnredeemedDeposit = $unredeemedDeposit->sum('amount');
                $unredeemedDepositCount = $unredeemedDeposit->count();

                /**
                 * Loan
                 */
                $loanItems = collect($employee->loans)->map(function ($loan) {
                    $loanItem = collect($loan->items)->first();
                    return $loanItem;
                })->filter(function ($salaryDepositItem) {
                    return $salaryDepositItem !== null;
                });

                $totalLoan = 0;
                // $totalLoan = $loanItems->sum('basic_payment');
                $loanCount = $loanItems->count();

                $additionalIncomes = [];
                $tunjanganHarianComponentAmount = $tunjanganHarianComponent['pivot']['amount'] ?? 0;
                if ($tunjanganHarianComponentAmount > 0) {
                    array_push($additionalIncomes, [
                        'name' => 'Tunjangan',
                        'type' => 'tunjangan_harian',
                        'value' => $tunjanganHarianComponentAmount,
                    ]);
                }

                $totalPeriods = collect($periods)->sum('total');
                $totalAdditionalIncomes = collect($additionalIncomes)->sum('value');

                // Summary
                $totalIncomes = $totalPeriods + $totalAdditionalIncomes;
                $totalDeductions = $lateCharge + $totalUnpaidDeposit + $totalLoan;
                $takeHomePay = $totalIncomes - $totalDeductions;

                $allLoanItems = collect($employee->loans)->flatMap(function ($loan) {
                    return $loan->items;
                })->all();

                $otherDeductions = [];

                return [
                    'employee' => collect($employee)->except('attendances')->all(),
                    'periods' => $periods,
                    'total_periods' => $totalPeriods,
                    'additional_incomes' => $additionalIncomes,
                    'total_time_late' => $totalTimeLate,
                    'late_charge' => $lateCharge,
                    // 'late_charge' => 0,
                    'number_of_days' => $numberOfDays + 1,
                    'total_unpaid_deposits' => $totalUnpaidDeposit,
                    'total_unredeemed_deposits' => $totalUnredeemedDeposit,
                    'total_loan' => $totalLoan,
                    // 'total_loan' => 0,
                    'include_deposit' => $unpaidDepositCount > 0 ? true : false,
                    'include_unredeemed_deposit' => false,
                    // 'include_loan' => $loanCount > 0 ? true : false,
                    'include_loan' => false,
                    'loans' => $employee->loans,
                    'loan_items' => $allLoanItems,
                    'selected_loan_id' => '',
                    'list_unpaid_deposits' => $unpaidDeposit->values()->all(),
                    'list_unredeemed_deposits' => $unredeemedDeposit->values()->all(),
                    'list_loans' => $loanItems->values()->all(),
                    'other_deductions' => $otherDeductions,
                    'summary' => [
                        'total_incomes' => $totalIncomes,
                        'total_deductions' => $totalDeductions,
                        'take_home_pay' => $takeHomePay,
                    ]
                ];
            })->all();

            // return $salaries[0];
            // Employees count
            $employeesCount = count($employees);
            $totalTakeHomePay = collect($salaries)->sum(function ($salary) {
                return $salary['summary']['take_home_pay'];
            });

            return response()->json([
                'message' => 'OK',
                'data' => [
                    'salaries' => $salaries,
                    'total_employees' => $employeesCount,
                    'total_take_home_pay' => $totalTakeHomePay,
                ],
            ]);
            // return view('daily-salaries.index', [
            //     'salaries' => $salaries,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save bulk daily salaries
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkSave(Request $request)
    {
        try {
            $salaries = $request->salaries;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $dailySalaries = collect($salaries)->map(function ($salary) use ($startDate, $endDate) {
                $employeeId = $salary['employee']['id'];

                $incomes = $salary['periods'];
                $deductions = [];
                if ($salary['late_charge'] > 0) {
                    array_push($deductions, [
                        'name' => 'Denda keterlambatan ' . $salary['total_time_late'] . ' Menit',
                        'value' => $salary['late_charge'],
                    ]);
                }

                $totalIncomes = $salary['summary']['total_incomes'];
                $totalDeductions = $salary['summary']['total_deductions'];
                $takeHomePay = $salary['summary']['take_home_pay'];

                return [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'type' => 'regular',
                    'incomes' => json_encode($incomes),
                    'deductions' => json_encode($deductions),
                    'total_incomes' => $totalIncomes,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $takeHomePay,
                    'description' => null,
                    'employee_id' => $employeeId,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->all();

            DB::table('daily_salaries')->insert($dailySalaries);

            return response()->json([
                'message' => 'Slip gaji telah tersimpan',
                'data' => $dailySalaries,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save bulk daily salaries aerplus
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkSaveAerplus(Request $request)
    {
        try {
            $salaries = $request->salaries;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // return [
            //     'salaryDepositItemsIds' => collect($salaryDepositItemsIds)->flatten()->all(),
            //     'salaryDepositIds' => collect($salaryDepositIds)->flatten()->all(),
            // ];

            $salaryDepositItemsIds = [];
            $salaryDepositIds = [];
            $loanItemsIds = [];

            $dailySalaries = collect($salaries)->map(function ($salary) use ($startDate, $endDate, &$salaryDepositItemsIds, &$salaryDepositIds, &$loanItemsIds) {
                $employeeId = $salary['employee']['id'];
                $officeId = $salary['employee']['office_id'] ?? null;

                $incomes = $salary['periods'];

                $additionalIncomes = $salary['additional_incomes'] ?? [];

                if ($salary['include_unredeemed_deposit'] == true) {
                    $rawSalaryDepositIds = collect($salary['list_unredeemed_deposits'])->pluck('id')->all();
                    $salaryDepositIds[] = $rawSalaryDepositIds;

                    $additionalIncomes[] = [
                        'name' => 'Pengembalian Deposit',
                        'type' => 'redeem_deposit',
                        'value' => $salary['total_unredeemed_deposits'],
                        'id' => $rawSalaryDepositIds,
                    ];
                }

                $deductions = [];
                if ($salary['late_charge'] > 0) {
                    array_push($deductions, [
                        'name' => 'Denda keterlambatan ' . $salary['total_time_late'] . ' Menit',
                        'type' => 'late_fee',
                        'value' => $salary['late_charge'],
                    ]);
                }

                $otherDeductions = $salary['other_deductions'] ?? [];
                foreach ($otherDeductions as $otherDeduction) {
                    array_push($deductions, [
                        'name' => 'Denda',
                        'type' => $otherDeduction['type'] ?? '',
                        'value' => $otherDeduction['amount'] ?? 0,
                    ]);
                }

                if ($salary['include_deposit'] == true) {
                    $rawSalaryDepositItemsIds = collect($salary['list_unpaid_deposits'])->pluck('id')->all();
                    $salaryDepositItemsIds[] = $rawSalaryDepositItemsIds;

                    array_push($deductions, [
                        'name' => 'Deposit',
                        'type' => 'deposit',
                        'value' => $salary['total_unpaid_deposits'],
                        'id' => $rawSalaryDepositItemsIds,
                    ]);
                }

                //! OLD
                // if ($salary['include_loan'] == true) {
                //     $rawLoanItemsIds = collect($salary['list_loans'])->pluck('id')->all();
                //     $loanItemsIds[] = $rawLoanItemsIds;

                //     array_push($deductions, [
                //         'name' => 'Kasbon',
                //         'type' => 'loan',
                //         'value' => $salary['total_loan'],
                //         'id' => $rawLoanItemsIds,
                //     ]);
                // }

                //* NEW
                if ($salary['include_loan'] == true) {
                    $rawLoanItemsIds = $salary['selected_loan_id'];
                    $loanItemsIds[] = $rawLoanItemsIds;

                    array_push($deductions, [
                        'name' => 'Kasbon',
                        'type' => 'loan',
                        'value' => $salary['total_loan'],
                        'id' => $rawLoanItemsIds,
                    ]);
                }

                $totalIncomes = $salary['summary']['total_incomes'];
                $totalDeductions = $salary['summary']['total_deductions'];
                $takeHomePay = $salary['summary']['take_home_pay'];

                return [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'type' => 'aerplus',
                    'incomes' => json_encode($incomes),
                    'additional_incomes' => json_encode($additionalIncomes),
                    'deductions' => json_encode($deductions),
                    'total_incomes' => $totalIncomes,
                    'total_deductions' => $totalDeductions,
                    'take_home_pay' => $takeHomePay,
                    'description' => null,
                    'employee_id' => $employeeId,
                    'office_id' => $officeId,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->all();

            DB::table('daily_salaries')->insert($dailySalaries);

            // collect($salaries)->each(function ($salary) use (&$salaryDepositItemsIds, &$salaryDepositIds) {
            //     if ($salary['include_deposit'] == true) {
            //     }

            //     if ($salary['include_unredeemed_deposit'] == true) {
            //     }
            // });

            SalaryDepositItem::whereIn('id', $salaryDepositItemsIds)
                ->update([
                    'paid' => 1,
                    'paid_date' => $endDate,
                ]);

            collect($salaryDepositIds)->each(function ($salaryDepositId) {
                $deposit = SalaryDeposit::query()->withSum(['items as item_paid_amount' => function ($q) {
                    $q->where('paid', 1);
                }], 'amount')->find($salaryDepositId);

                $deposit->redeemed = 1;
                $deposit->redeemed_amount = $deposit->item_paid_amount;
                $deposit->redeemed_date = date('Y-m-d');
                $deposit->save();
                // $salaryDeposit = 
                // SalaryDeposit::whereIn('id', $salaryDepositIds)
                //     ->update(['redeemed' => 1, 'redeemed_date' => $endDate,]);
            });

            LoanItem::whereIn('id', $loanItemsIds)
                ->update(['paid' => 1]);


            return response()->json([
                'message' => 'Slip gaji telah tersimpan',
                'data' => $dailySalaries,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export aerplus report
     */
    public function exportAerplusReport(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if ($startDate == null && $endDate == null) {
                throw new Error('params "start_date" and "end_date" are required');
            }

            $response = Http::get(env('OPERATIONAL_URL') . '/api/v1/outlets/office-sync');

            $outlets = [];
            if ($response->successful()) {
                $outlets = $response->json()['data'];
            }

            // return $outlets;

            $dailySalaries = DailySalary::with(['office', 'employee' => function ($employeeQuery) {
                $employeeQuery->with(['office', 'bankAccounts']);
            }])
                ->where('type', 'aerplus')
                ->where('start_date', $startDate)
                ->where('end_date', $endDate)
                ->get()
                ->map(function ($dailySalary) use ($outlets) {
                    $employeeName = '';
                    $bankAccountOwner = '';
                    $bankAccountNumber = '';
                    $office = null;
                    $totalIncomes = $dailySalary->total_incomes;
                    $totalAdditionalIncomes = collect(json_decode($dailySalary->additional_incomes ?? "[]", true))->sum('value');
                    $totalDeductions = $dailySalary->total_deductions;
                    $takeHomePay = $dailySalary->take_home_pay;

                    $bankAccountOwner = 'ok';
                    $bankAccountNumber = 'ok';

                    if ($dailySalary->employee !== null) {
                        $employee = $dailySalary->employee;
                        $employeeName = $employee->name ?? '';
                        $bankAccounts = $employee->bankAccounts ?? [];
                        $defaultBankAccount = collect($bankAccounts)->first();
                        $bankAccountOwner = $defaultBankAccount->account_owner ?? '';
                        $bankAccountNumber = $defaultBankAccount->account_number ?? '';

                        // if (($employee->office ?? null) !== null) {
                        //     $office = $employee->office->name ?? '';
                        // }
                    }

                    if ($dailySalary->office != null) {
                        $office = $dailySalary->office->name;
                    } else {
                        if (($employee->office ?? null) !== null) {
                            $office = $employee->office->name ?? '';
                        }
                    }

                    // return $employee;
                    return [
                        'name' => $employeeName,
                        'bank_account_owner' => $bankAccountOwner,
                        'bank_account_number' => $bankAccountNumber,
                        'office' => $office,
                        'additional_incomes' => $dailySalary->additional_incomes,
                        'total_daily' => $totalIncomes - $totalAdditionalIncomes,
                        'deductions' => $dailySalary->deductions,
                        'total_incomes' => $totalIncomes,
                        'total_deductions' => $totalDeductions,
                        'take_home_pay' => $takeHomePay,
                        'office_id' => $employee->office_id
                    ];
                })
                ->sortBy('office_id')
                ->groupBy('office')
                ->map(function ($employees, $officeName) use ($outlets) {
                    $officeId = $employees[0]['office_id'] ?? 0;
                    $cvName = $outlets[$officeId]['cv']['name'] ?? 'CV';
                    return [
                        'cv_name' => $cvName,
                        'office' => $officeName,
                        'employees' => $employees,
                    ];
                })
                // ->sortBy(function ($data) {
                //     return $data['office']['id'];
                // })
                ->values()
                ->all();

            // return $dailySalaries;

            return Excel::download(new AerplusDailySalaryExport($startDate, $endDate, $dailySalaries), 'Rekapitulasi Gaji Harian' . $startDate . ' - ' . $endDate . '.xlsx');

            // return response()->json([
            //     'message' => 'OK',
            //     'data' => $dailySalaries,
            // ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Export aerplus report
     */
    public function exportAerplusSummaryReport(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if ($startDate == null && $endDate == null) {
                throw new Error('params "start_date" and "end_date" are required');
            }

            $response = Http::get(env('OPERATIONAL_URL') . '/api/v1/outlets/office-sync');

            $outlets = [];
            if ($response->successful()) {
                $outlets = $response->json()['data'];
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

            $offices = Office::all();

            $dailySalariesByOffices = collect($dailySalaries)
                ->sortBy(function ($dailySalary) {
                    return $dailySalary->office_id ?? $dailySalary->employee->office->id ?? 0;
                })
                ->groupBy(function ($dailySalary) {
                    return $dailySalary->office_id ?? $dailySalary->employee->office->id ?? 0;
                })->map(function ($dailySalaries, $officeId) use ($offices, $startDate, $endDate, $outlets) {
                    $cvName = $outlets[$officeId]['cv']['name'] ?? '-';
                    $totalOutletTakeHomePay = collect($dailySalaries)->sum('take_home_pay');
                    $officeName = collect($offices)->where('id', $officeId)->first()->name ?? '';
                    return [
                        'cv_name' => $cvName,
                        "depot" => $officeName,
                        'formatted_total' => number_format($totalOutletTakeHomePay, 0, '.', ','),
                        "total" => $totalOutletTakeHomePay,
                    ];
                })->values()->all();

            // return [
            //     'distribusi' => $outletSpendingPayloads2,
            //     'total' => number_format(collect($outletSpendingPayloads2)->sum('total'), 0, '.', ','),
            // ];

            // return $dailySalaries;

            return Excel::download(new AerplusDailySalarySummaryExport($startDate, $endDate, $dailySalariesByOffices), 'Rekapitulasi Gaji Harian Summary Periode ' . $startDate . ' - ' . $endDate . '.xlsx');

            // return response()->json([
            //     'message' => 'OK',
            //     'data' => $dailySalaries,
            // ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Export report
     */
    public function exportMagentaReport(Request $request)
    {
        try {
            $month = $request->query('month');
            $year = $request->query('year');

            if ($month == null && $year == null) {
                throw new Error('params "month" and "year" are required');
            }

            $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            $setting = MagentaDailySalarySetting::first();

            return Excel::download(new MagentaDailySalaryExport($month, $year, $setting), 'Laporan Gaji Harian Bulan ' . $months[(int) $month - 1] . ' ' . $year . '.xlsx');
        } catch (\Throwable $th) {
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
     * Print magenta
     */
    public function print($id)
    {
        $salary = DailySalary::findOrFail($id);
        $salary->incomes = json_decode($salary->incomes);
        $salary->additional_incomes = json_decode($salary->additional_incomes);
        $salary->deductions = json_decode($salary->deductions);

        $employee = Employee::with(['activeCareer.jobTitle.designation.department'])->find($salary->employee_id);

        if ($employee == null) {
            abort(500);
        }

        $eventCalendars = collect($salary->incomes)->flatMap(function ($income) {
            return $income?->event_calendars ?? [];
        })->all();

        $data = [
            'employee' => $employee,
            'final_payslip' => $salary,
            'event_calendars' => $eventCalendars,
        ];
        // return $data;

        $pdf = PDF::loadView('exports.pdf.magenta-daily-payslip', $data);
        // return $pdf->stream();
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Gaji ' . $employee->name . '.pdf');
        // $mpdf = new \Mpdf\Mpdf();
        // $mpdf->load
        // $mpdf->WriteHTML('<h1>Hello world!</h1>');
        // $mpdf->Output();
    }

    /**
     * Update paid status
     */
    public function pay(Request $request)
    {
        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $paymentBatchCode = $request->payment_batch_code;

            DailySalary::query()
                ->where('start_date', $startDate)
                ->where('end_date', $endDate)
                ->where('type', 'aerplus')
                ->update([
                    'paid' => 1,
                    'payment_batch_code' => $paymentBatchCode,
                ]);

            return response()->json([
                'message' => 'Pembayaran berhasil',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }
}
