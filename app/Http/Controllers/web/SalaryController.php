<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Exports\salaries\SalaryMonthlyExport;
use App\Exports\salaries\SalaryMonthlyExportV2;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\LeaveApplication;
use App\Models\Salary;
use App\Models\SalaryComponent;
use App\Models\SalaryItem;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel as Excel;
use PDF;

class SalaryController extends Controller
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
        $salary = Salary::with(['items', 'employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'office.division.company']);
        }])->findOrFail($id);
        $salaryComponents = SalaryComponent::all();
        return view('salaries.edit', [
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
        DB::beginTransaction();
        try {
            $requestSalaryItems = $request->salary_items;
            $requestNewSalaryItems = $request->new_salary_items;

            $salary = Salary::find($id);
            $salaryItems = $salary->items;
            $salaryItemsIds = collect($salaryItems)->pluck('id');
            $requestSalaryItemsIds = collect($requestSalaryItems)->pluck('id');
            $diffSalaryItemsIds = collect($salaryItemsIds)->diff($requestSalaryItemsIds)->values()->all();

            $newSalaryItems = collect($requestNewSalaryItems)->filter(function ($newSalaryItem) {
                return $newSalaryItem['component'] !== null;
            })->map(function ($newSalaryItem) use ($id) {
                return [
                    'name' => $newSalaryItem['component']['name'],
                    'type' => $newSalaryItem['component']['type'],
                    'salary_type' => $newSalaryItem['component']['salary_type'],
                    'amount' => $newSalaryItem['amount'],
                    'salary_id' => $id,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->all();

            // return response()->json([
            //     'data' => $newSalaryItems,
            // ]);

            if (count($diffSalaryItemsIds) > 0) {
                SalaryItem::query()->whereIn('id', [$diffSalaryItemsIds])->delete();
            }

            DB::table('salary_items')->insert($newSalaryItems);

            DB::commit();

            return response()->json([
                'message' => 'Data telah tersimpan',
            ]);
        } catch (\Throwable $e) {
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
        DB::beginTransaction();
        try {
            $salary = Salary::findOrFail($id);
            LeaveApplication::where('salary_id', $id)->delete();
            $salary->delete();

            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $salaryIds = $request->ids;
            // return response()->json([
            //     'ids' => gettype($salaryIds),
            // ]);
            if (is_array($salaryIds)) {
                Salary::query()->whereIn('id', $salaryIds)->delete();
                LeaveApplication::query()->whereIn('salary_id', $salaryIds)->delete();
            } else {
                throw new Error('ids must be an array');
            }

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate salaries
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {

        $salaryStartDayPeriod = 26;
        $salaryStartMonthPeriod = $request->query('month');
        $salaryStartYearPeriod = $request->query('year');

        // $startDate = $request->query('start_date');
        // $endDate = $request->query('end_date');

        if ($salaryStartMonthPeriod == null || $salaryStartYearPeriod == null) {
            return response()->json([
                'message' => 'Bulan dan tahun diperlukan',
            ], 400);
        }

        $carbonDate = Carbon::parse($salaryStartYearPeriod . '-' . $salaryStartMonthPeriod . '-' . $salaryStartDayPeriod);

        $startDate = $carbonDate->toDateString();
        $endDate = $carbonDate->addMonth()->subDay()->toDateString();

        // return [
        //     'start_date' => $startDate,
        //     'end_date' => $endDate,
        // ];

        try {
            $employees = Employee::with([
                'attendances' => function ($q) {
                    $q->orderBy('id', 'desc');
                },
                'activeWorkingPatterns.items', 'loans.items', 'salaryComponents', 'leave'
            ])->get();

            // return $employees;

            $eventCalendars = EventCalendar::all();

            $periodDates = $this->getDatesFromRange($startDate, $endDate);

            $salaries = collect($employees)->map(function ($employee) use ($periodDates, $eventCalendars, $endDate) {
                $isGetPresenceIncentive = false;

                $attendances = $employee->attendances;
                $workingPatterns = $employee->activeWorkingPatterns;
                $salaryComponents = $employee->salaryComponents;
                $loans = $employee->loans;
                $leave = $employee->leave;
                $totalLoans = collect($loans)->sum('amount');

                $mappedLoanItems = collect($loans)->flatMap(function ($loan) {
                    return $loan['items'];
                });

                // $loanItems = $mappedLoanItems->all();

                $currentMonthLoanItems = $mappedLoanItems->where('payment_date', $endDate)->all();

                $incomes = collect($salaryComponents)->where('type', 'income')->map(function ($income) {
                    return [
                        'name' => $income['name'],
                        'type' => $income['salary_type'],
                        'amount' => $income['pivot']['amount'],
                    ];
                })->all();
                $deductions = collect($salaryComponents)->where('type', 'deduction')->map(function ($income) {
                    return [
                        'name' => $income['name'],
                        'type' => $income['salary_type'],
                        'amount' => $income['pivot']['amount'],
                    ];
                })->all();

                $activeWorkingPattern = collect($workingPatterns)->first();
                $notPresentDays = [];

                // !DEVELOPMENT ONLY
                $employeeWage = [
                    'daily' => 50000,
                    'daily_coefficient' => 2,
                    'overtime' => 5000,
                    'overtime_coefficient' => 2,
                ];

                $periods = collect($periodDates)->each(function ($date) use ($attendances, $activeWorkingPattern, $eventCalendars, &$notPresentDays) {
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

                        if (!$eventCalendarsExist || !$isHoliday) {
                            array_push($notPresentDays, [
                                'date' => $date,
                                'description' => 'Tidak hadir (tanpa status)',
                                'type' => 'no_status',
                            ]);
                        }
                    } else {
                        if ($newestAttendance['status'] == 'hadir') {
                            $timeLate = (int) $newestAttendance['time_late'];

                            if ($workingPatternDay !== null) {
                                // When status is holiday
                                if ($workingPatternDay['day_status'] == 'workday' && $timeLate > 0) {
                                    array_push($notPresentDays, [
                                        'date' => $date,
                                        'description' => 'Terlambat (' . $timeLate . ' menit)',
                                        'time_late' => $timeLate,
                                        'type' => 'late',
                                    ]);
                                }
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

                $presenceIncentiveAmount = 0;
                if ($isGetPresenceIncentive) {
                    $presenceIncentiveAmount = $employeeWage['daily'] * 2;
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
                if ($leave !== null) {
                    if (isset($leave['total']) && isset($leave['taken'])) {
                        $remainingLeave = $leave['total'] - $leave['taken'];
                        // round($basicSalary->value / 26) * $item->excess_leave;
                        $excessLeave = abs($remainingLeave - $noStatusAttendanceCount);
                        $basicSalary = collect($incomes)->where('type', 'gaji_pokok')->first();
                        $excessLeaveCharge = ($basicSalary['amount'] / 26) * $excessLeave;

                        $deductions = collect($deductions)->push([
                            'name' => 'Kelebihan Cuti',
                            'type' => 'excess_leave',
                            'amount' => round($excessLeaveCharge),
                        ])->all();
                    }
                }
                // End: Excess Leave

                // Summary
                $totalIncomes = collect($incomes)->sum('amount');
                $totalDeductions = collect($deductions)->sum('amount');
                $takeHomePay = $totalIncomes - $totalDeductions;

                return [
                    'employee_id' => collect($employee)->except('attendances')->all(),
                    // 'periods' => $periods,
                    // 'not_present_days' => $notPresentDays,
                    'incomes' => $incomes,
                    'deductions' => $deductions,
                    'attributes' => [
                        'is_get_presence_incentive' => $isGetPresenceIncentive,
                        'excess_leave' => $excessLeave,
                    ],
                    'summary' => [
                        'total_incomes' => $totalIncomes,
                        'total_deductions' => $totalDeductions,
                        'take_home_pay' => $takeHomePay,
                        'total_loans' => $totalLoans,
                    ],
                    // 'loans' => $loans,
                    // 'loan_items' => $currentMonthLoanItems,
                ];
            })->all();

            return response()->json([
                'salaries' => $salaries,
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
     * Generate bulk save
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $salaries = $request->salaries;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $salaryItems = [];

            $leaveApplications = [];
            $leaveApplicationConfirmedBy = Auth::user()->employee->id ?? null;
            $currentTime = Carbon::now()->toDateTimeString();

            foreach ($salaries as $salary) {
                $newSalary = new Salary();
                $newSalary->start_date = $startDate;
                $newSalary->end_date = $endDate;
                $newSalary->employee_id = $salary['employee']['id'];
                $newSalary->save();

                $incomes = collect($salary['incomes'])->map(function ($income) use ($newSalary, $currentTime) {
                    return [
                        'name' => $income['name'],
                        'type' => 'income',
                        'salary_type' => $income['type'],
                        'salary_type_amount' => $income['type_amount'] ?? null,
                        'salary_type_unit' => $income['type_unit'] ?? null,
                        'amount' => $income['amount'],
                        'salary_id' => $newSalary->id,
                        'loan_item_id' => NULL,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                    ];
                })->all();

                $salaryItems = collect($salaryItems)->concat($incomes)->all();

                $deductions = collect($salary['deductions'])->map(function ($deduction) use ($newSalary, $startDate, $endDate, &$leaveApplications, $salary, $leaveApplicationConfirmedBy, $currentTime) {
                    $loanId = NULL;
                    if ($deduction['type'] == 'loan') {
                        if (isset($deduction['loan_id'])) {
                            $loanId = $deduction['loan_id'];
                        }
                    }

                    // if ($deduction['type'] == 'excess_leave') {
                    //     if (count($deduction['application_dates']) > 0) {
                    //         $leaveApplications[] = [
                    //             'date' => $endDate,
                    //             'application_dates' => implode(',', $deduction['application_dates']),
                    //             'note' => 'Kelebihan Cuti Penggajian Periode ' . $startDate . ' - ' . $endDate,
                    //             'approval_status' => 'approved',
                    //             'employee_id' => $salary['employee']['id'],
                    //             'leave_category_id' => 7,
                    //             'confirmed_by' => $leaveApplicationConfirmedBy,
                    //             'confirmed_at' => $currentTime,
                    //             'salary_id' => $newSalary->id,
                    //             'created_at' => $currentTime,
                    //             'updated_at' => $currentTime,
                    //         ];
                    //     }
                    // }

                    return [
                        'name' => $deduction['name'],
                        'type' => 'deduction',
                        'salary_type' => $deduction['type'],
                        'salary_type_amount' => $deduction['type_amount'] ?? null,
                        'salary_type_unit' => $deduction['type_unit'] ?? null,
                        'amount' => $deduction['amount'],
                        'salary_id' => $newSalary->id,
                        'loan_item_id' => $loanId,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                    ];
                })->all();

                $salaryItems = collect($salaryItems)->concat($deductions)->all();

                if (count($salary['leave_application_dates']) > 0) {
                    $leaveApplications[] = [
                        'date' => $endDate,
                        'application_dates' => implode(',', $salary['leave_application_dates']),
                        'note' => 'Kelebihan Cuti Penggajian Periode ' . $startDate . ' - ' . $endDate,
                        'approval_status' => 'approved',
                        'employee_id' => $salary['employee']['id'],
                        'leave_category_id' => 7,
                        'confirmed_by' => $leaveApplicationConfirmedBy,
                        'confirmed_at' => $currentTime,
                        'salary_id' => $newSalary->id,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                    ];
                }
            }

            DB::table('salary_items')->insert($salaryItems);
            DB::table('leave_applications')->insert($leaveApplications);

            DB::commit();

            return response()->json([
                'message' => 'Slip gaji telah tersimpan',
                // 'data' => $salaryItems,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export report monthly
     */
    public function exportMonthlyReport(Request $request)
    {
        $startDatePeriod = $request->query('start_date_period');
        $endDatePeriod = $request->query('end_date_period');
        // $staffOnly = $request->query('staffonly');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $companyId = request()->query('company_id');
        $presenceIncentiveOnly = request()->query('presence_incentive_only') ?? 0;
        $staffOnly = $staffOnly == 'true' ? true : false;

        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        // $startDate = $startDatePeriod;
        // $endDate = $endDatePeriod;
        // $company = Company::query()->first();

        // $sheetCompanyId = 25;
        // // $sheetCompanyId = null;
        // // if (isset($company->id)) {
        // //     $sheetCompanyId = $company->id;
        // // }

        // $employees = Employee::query()->whereHas('salaries', function ($query) use ($startDate, $endDate) {
        //     $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
        // })
        //     ->with([
        //         'activeCareer.jobTitle', 'office.division.company',
        //         'loans' => function ($q) use ($endDate) {
        //             $q->with(['items' => function ($itemQuery) use ($endDate) {
        //                 $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
        //             }]);
        //         },
        //         'salaries' => function ($query) use ($startDate, $endDate) {
        //             $query->where('start_date', $startDate)->where('end_date', $endDate);
        //         }
        //     ])
        //     ->get()
        //     ->filter(
        //         function ($employee) use ($sheetCompanyId) {
        //             $employeeCompanyId = null;
        //             if (isset($employee?->office?->division?->company?->id)) {
        //                 $employeeCompanyId = $employee?->office?->division?->company?->id;
        //             }

        //             return $employeeCompanyId == $sheetCompanyId;
        //         }
        //     )
        //     ->map(function ($employee) {
        //         $basicSalary = 0;
        //         $positionAllowance = 0;
        //         $attendanceAllowance = 0;
        //         $currentMonthLoans = 0;
        //         $excessLeave = 0;
        //         $total = 0;

        //         $totalLoan = collect($employee->loans)->sum('amount');
        //         // $totalPaidLoan = collect($employee->loans)->pluck('items')->filter(function ($item) {
        //         //     return $item->salary_item_count > 0;
        //         // })->sum('amount');
        //         $totalPaidLoan = collect($employee->loans)->flatMap(function ($loan) {
        //             return $loan->items;
        //         })->filter(function ($item) {
        //             return $item->salary_item_count > 0;
        //         })->sum('basic_payment');

        //         $remainingLoan = $totalLoan - $totalPaidLoan;
        //         // $remainingLoan = $totalLoan - 0;

        //         $salariesDataCount = collect($employee->salaries)->count();
        //         if ($salariesDataCount > 0) {
        //             $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
        //                 return $salary->items;
        //             });
        //             $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
        //             $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
        //             $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
        //             $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
        //             $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
        //             $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);

        //             $employee->basic_salary = $basicSalary;
        //             $employee->position_allowance = $positionAllowance;
        //             $employee->attendance_allowance = $attendanceAllowance;
        //             $employee->loan = $currentMonthLoans;
        //             $employee->total_loan = $totalLoan;
        //             $employee->total_paid_loan = $totalPaidLoan;
        //             $employee->loan_balance = $remainingLoan;
        //             $employee->excess_leave = $excessLeave;
        //             $employee->total = $total;

        //             return $employee;
        //         }
        //     })->all();

        // return collect($employees)->where('id', 31)->first();
        $company = Company::find($companyId);
        // return $company;
        $fileName = 'Laporan Gaji Bulanan Periode ' . $startDatePeriod . ' - ' . $endDatePeriod . ' ' . ($company->name ?? '') . '.xlsx';

        if ($presenceIncentiveOnly == 1) {
            $fileName = 'Insentif Kehadiran Staff Periode ' . $startDatePeriod . ' - ' . $endDatePeriod . ' ' . ($company->name ?? '') . '.xlsx';
        }

        return Excel::download(new SalaryMonthlyExportV2($startDatePeriod, $endDatePeriod, $staffOnly, $haveStaffPermission, $companyId, $presenceIncentiveOnly), $fileName);
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
     * Print payslip
     */
    public function print($id)
    {
        // $finalPayslip = FinalPayslip::findOrFail($id);
        // $finalPayslip->income = json_decode($finalPayslip->income);
        // $finalPayslip->deduction = json_decode($finalPayslip->deduction);

        // $employeeColumns = ['id', 'employee_id', 'first_name', 'last_name', 'work_placement', 'start_work_date', 'photo', 'daily_money_regular', 'daily_money_holiday', 'overtime_pay_regular', 'overtime_pay_holiday'];

        // $employee = Employee::query()->select($employeeColumns)->with(['careers' => function ($query) {
        //     $query->with(['jobTitle', 'designation', 'department'])->where('is_active', 1);
        // }])->find($finalPayslip->employee_id);

        // if ($employee == null) {
        //     abort(500);
        // }
        // return $finalPayslip;

        $salary = Salary::with(['items'])->findOrFail($id);
        $employee = Employee::with(['activeCareer.jobTitle.designation.department'])->find($salary->employee_id);

        $data = [
            'employee' => $employee,
            'final_payslip' => $salary,
        ];
        // return $data;

        $pdf = PDF::loadView('exports.pdf.monthly-payslip', $data);
        return $pdf->stream();
    }

    /**
     * Print payslip
     */
    public function printV2($id)
    {


        $salary = Salary::with(['items'])->findOrFail($id);
        $startDate = $salary->start_date;
        $endDate = $salary->end_date;
        $employee = Employee::with(['activeCareer.jobTitle.designation.department', 'loans' => function ($q) use ($endDate) {
            $q->with(['items' => function ($itemQuery) use ($endDate) {
                $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
            }]);
        },])->find($salary->employee_id);



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

        $salaryItems = collect($salary->items);

        // return $salaryItems;

        $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
        $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
        $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
        $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->where('type', 'deduction')->sum('amount');
        $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
        $total = ($basicSalary + $positionAllowance + $attendanceAllowance + $currentMonthSingleLoan) - ($currentMonthLoans + $excessLeave);

        // Gaji Pokok
        $salary->basic_salary = $basicSalary;
        // Tunjangan
        $salary->position_allowance = $positionAllowance;
        // Insentif Kehadiran
        $salary->attendance_allowance = $attendanceAllowance;
        // Bruto
        $salary->bruto_salary = $basicSalary + $positionAllowance + $attendanceAllowance;
        // Piutang Awal
        $salary->total_loan = $totalLoan;
        // Piutang Pinjaman
        $salary->current_month_loan = $currentMonthSingleLoan;
        // Piutang Potongan
        $salary->loan = $currentMonthLoans;
        // Piutang Sisa
        $salary->remaining_loan = $remainingLoan;
        // Total Paid Loan
        $salary->total_paid_loan = $totalPaidLoan;
        // Kelebihan Cuti
        $salary->excess_leave = $excessLeave;
        // Netto
        $salary->total = $total;

        // return $salary;

        //         Perincian Piutang	
        // Awal	 500.000 
        // Pinjaman	 - 
        // Akhir	 400.000 


        $data = [
            'employee' => $employee,
            'salary' => $salary,
        ];
        // return $data;

        $pdf = PDF::loadView('exports.pdf.v2.monthly-payslip', $data);
        // return $pdf->stream();
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Gaji ' . $employee->name . '.pdf');
    }

    public function pay(Request $request)
    {
        try {
            $salaryIds = $request->salary_ids;

            Salary::whereIn('id', $salaryIds)->update([
                'paid' => 1,
                'last_paid_by' => Auth::id(),
                'last_paid_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Pembayaran berhasil',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function unpaid(Request $request)
    {
        try {
            $salaryIds = $request->salary_ids;

            Salary::whereIn('id', $salaryIds)->update([
                'paid' => 0,
                'last_unpaid_by' => Auth::id(),
                'last_unpaid_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Perubahan dibatalkan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
