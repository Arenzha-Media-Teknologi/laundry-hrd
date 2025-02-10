<?php

namespace App\Http\Controllers\web;

use App\Exports\payrollbca\MultiPayrollTemplateDailyExport;
use App\Exports\payrollbca\MultiPayrollTemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DailySalary;
use App\Models\Employee;
use App\Models\EventCalendar;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class PayrollBcaController extends Controller
{
    public function index()
    {
        return view('payroll-bca.index');
    }

    public function monthly()
    {
        $request = request();
        // Generated
        $currentDate = date('Y-m-d');
        $salaryStartDayPeriod = 26;
        $salaryStartMonthPeriod = $request->query('month') ?? date('m');
        $salaryStartYearPeriod = $request->query('year') ?? date('Y');
        $companyId = $request->query('company_id');
        $staffOnly = $request->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;

        if ($salaryStartMonthPeriod == null || $salaryStartYearPeriod == null) {
            return response()->json([
                'message' => 'Bulan dan tahun diperlukan',
            ], 400);
        }

        $carbonDate = Carbon::parse($salaryStartYearPeriod . '-' . $salaryStartMonthPeriod . '-' . $salaryStartDayPeriod);

        $startDate = $carbonDate->subMonth()->toDateString();
        $endDate = $carbonDate->addMonth()->subDay()->toDateString();

        // $staffOnly = false;
        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $userGroup = auth()->user()->employee->credential->group ?? null;
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

        if (!$haveStaffPermission) {
            $employeesHasSalariesCollection->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $employeesHasSalariesCollection->where('type', 'staff');
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

                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $employee->transaction_id = $transactionId;
                $employee->transfer_type = "BCA";
                $employee->benificiary_id = "";
                $employee->credited_account = $employee->name;
                $employee->bank_account_number = $employee->bankAccounts[0]->account_number ?? "";
                $employee->amount = $total;
                $employee->nip = "";
                $employee->remark = "";
                $employee->benificiary_email_address = "";
                $employee->receiver_swift_code = "";
                $employee->receiver_cust_type = "";
                $employee->receiver_cust_residence = "";

                return $employee;
            })->all();

        // return $salariesSummaryEmployees;

        $errorRows = collect($salariesSummaryEmployees)->filter(function ($salary) {
            $isClear = true;

            if (empty($salary->transaction_id)) {
                $isClear = false;
            }

            if (empty($salary->transfer_type)) {
                $isClear = false;
            }

            if (empty($salary->credited_account)) {
                $isClear = false;
            }

            if (empty($salary->bank_account_number)) {
                $isClear = false;
            }

            if (empty($salary->amount)) {
                $isClear = false;
            }

            if ($salary->transfer_type == 'LLG' || $salary->transfer_type == 'RTGS') {
                if (empty($salary->receiver_swift_code) || empty($salary->receiver_cust_type) || empty($salary->receiver_cust_residence)) {
                    $isClear = false;
                }
            }

            return $isClear == false;
        })->all();

        // return $errorRows;

        $company = Company::with(['businessType'])->find($companyId);

        // return $company;

        // return $salariesSummaryEmployees;

        return view('payroll-bca.monthly.index', [
            'error_rows' => $errorRows,
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
     * Show by period
     */
    public function dailyMagenta(Request $request)
    {
        $startDate = $request->query('start_date') ?? date('Y-m-d');
        $endDate = $request->query('end_date') ?? date('Y-m-d');
        $companyId = $request->query('company_id');

        $MAGENTA_ID = 1;

        $company = Company::with(['businessType'])->find($MAGENTA_ID);

        // if ($startDate == null || $endDate == null) {
        //     abort(404);
        // }

        $dailySalaries = DailySalary::with(['employee.activeCareer.jobTitle'])
            ->where('type', 'regular')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get()->each(function (DailySalary $dailySalary) {
                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $total = $dailySalary->take_home_pay;

                $dailySalary->transaction_id = $transactionId;
                $dailySalary->transfer_type = "BCA";
                $dailySalary->benificiary_id = "";
                $dailySalary->credited_account = $dailySalary->employee->name;
                $dailySalary->bank_account_number = $dailySalary->employee->bankAccounts[0]->account_number ?? "";
                $dailySalary->amount = $total;
                $dailySalary->nip = "";
                $dailySalary->remark = "";
                $dailySalary->benificiary_email_address = "";
                $dailySalary->receiver_swift_code = "";
                $dailySalary->receiver_cust_type = "";
                $dailySalary->receiver_cust_residence = "";

                // return $dailySalary;
            });

        $errorRows = collect($dailySalaries)->filter(function ($salary) {
            $isClear = true;

            if (empty($salary->transaction_id)) {
                $isClear = false;
            }

            if (empty($salary->transfer_type)) {
                $isClear = false;
            }

            if (empty($salary->credited_account)) {
                $isClear = false;
            }

            if (empty($salary->bank_account_number)) {
                $isClear = false;
            }

            if (empty($salary->amount)) {
                $isClear = false;
            }

            if ($salary->transfer_type == 'LLG' || $salary->transfer_type == 'RTGS') {
                if (empty($salary->receiver_swift_code) || empty($salary->receiver_cust_type) || empty($salary->receiver_cust_residence)) {
                    $isClear = false;
                }
            }

            return $isClear == false;
        })->all();

        $periods = DailySalary::select('start_date', 'end_date', DB::raw('COUNT(*) as total'))
            ->where('type', 'regular')
            ->groupBy('start_date', 'end_date')
            ->get()->each(function ($period) {
                $period->year = Carbon::parse($period->end_date)->format('Y');
            });

        // return $periods;

        $takeHomePay = collect($dailySalaries)->sum('take_home_pay');

        return view('payroll-bca.daily-magenta.index', [
            'error_rows' => $errorRows,
            'periods' => $periods,
            'company' => $company,
            'salaries' => $dailySalaries,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'take_home_pay' => $takeHomePay,
            'company_id' => $MAGENTA_ID,
        ]);
    }

    /**
     * Show by period
     */
    public function dailyAerplus(Request $request)
    {
        $startDate = $request->query('start_date') ?? date('Y-m-d');
        $endDate = $request->query('end_date') ?? date('Y-m-d');
        $companyId = $request->query('company_id');

        $AERPLUS_ID = 1;

        $company = Company::with(['businessType'])->find($AERPLUS_ID);

        // if ($startDate == null || $endDate == null) {
        //     abort(404);
        // }

        $dailySalaries = DailySalary::with(['employee.activeCareer.jobTitle'])
            ->where('type', 'aerplus')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get()->each(function (DailySalary $dailySalary) {
                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $total = $dailySalary->take_home_pay;

                $dailySalary->transaction_id = $transactionId;
                $dailySalary->transfer_type = "BCA";
                $dailySalary->benificiary_id = "";
                $dailySalary->credited_account = $dailySalary->employee->name;
                $dailySalary->bank_account_number = $dailySalary->employee->bankAccounts[0]->account_number ?? "";
                $dailySalary->amount = $total;
                $dailySalary->nip = "";
                $dailySalary->remark = "";
                $dailySalary->benificiary_email_address = "";
                $dailySalary->receiver_swift_code = "";
                $dailySalary->receiver_cust_type = "";
                $dailySalary->receiver_cust_residence = "";

                // return $dailySalary;
            });

        $errorRows = collect($dailySalaries)->filter(function ($salary) {
            $isClear = true;

            if (empty($salary->transaction_id)) {
                $isClear = false;
            }

            if (empty($salary->transfer_type)) {
                $isClear = false;
            }

            if (empty($salary->credited_account)) {
                $isClear = false;
            }

            if (empty($salary->bank_account_number)) {
                $isClear = false;
            }

            if (empty($salary->amount)) {
                $isClear = false;
            }

            if ($salary->transfer_type == 'LLG' || $salary->transfer_type == 'RTGS') {
                if (empty($salary->receiver_swift_code) || empty($salary->receiver_cust_type) || empty($salary->receiver_cust_residence)) {
                    $isClear = false;
                }
            }

            return $isClear == false;
        })->all();

        $periods = DailySalary::select('start_date', 'end_date', DB::raw('COUNT(*) as total'))
            ->where('type', 'aerplus')
            ->groupBy('start_date', 'end_date')
            ->get()->each(function ($period) {
                $period->year = Carbon::parse($period->end_date)->format('Y');
            });

        // return $periods;

        $takeHomePay = collect($dailySalaries)->sum('take_home_pay');

        return view('payroll-bca.daily-aerplus.index', [
            'error_rows' => $errorRows,
            'periods' => $periods,
            'company' => $company,
            'salaries' => $dailySalaries,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'take_home_pay' => $takeHomePay,
            'company_id' => $AERPLUS_ID,
        ]);
    }


    public function dailyMagenta2()
    {
        $request = request();
        // Generated
        $currentDate = date('Y-m-d');
        $salaryStartDayPeriod = 26;
        $salaryStartMonthPeriod = $request->query('month') ?? date('m');
        $salaryStartYearPeriod = $request->query('year') ?? date('Y');
        $companyId = $request->query('company_id');
        $staffOnly = $request->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;

        if ($salaryStartMonthPeriod == null || $salaryStartYearPeriod == null) {
            return response()->json([
                'message' => 'Bulan dan tahun diperlukan',
            ], 400);
        }

        $carbonDate = Carbon::parse($salaryStartYearPeriod . '-' . $salaryStartMonthPeriod . '-' . $salaryStartDayPeriod);

        $startDate = $carbonDate->subMonth()->toDateString();
        $endDate = $carbonDate->addMonth()->subDay()->toDateString();

        // $staffOnly = false;
        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $userGroup = auth()->user()->employee->credential->group ?? null;
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

        if (!$haveStaffPermission) {
            $employeesHasSalariesCollection->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $employeesHasSalariesCollection->where('type', 'staff');
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

        $company = Company::with(['businessType'])->find($companyId);

        // return $company;

        // return $salariesSummaryEmployees;

        return view('payroll-bca.monthly.index', [
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
     * Export report monthly
     */
    public function exportMultiPayrollTemplate(Request $request)
    {
        $startDatePeriod = $request->query('start_date_period');
        $endDatePeriod = $request->query('end_date_period');
        // $staffOnly = $request->query('staffonly');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $companyId = request()->query('company_id');
        $staffOnly = $staffOnly == 'true' ? true : false;

        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $company = Company::find($companyId);
        // return $company;
        $fileName = 'TEMPLATE MULTI PAYROLL HONOR ' . $startDatePeriod . ' - ' . $endDatePeriod . ' ' . ($company->name ?? '') . '.xlsx';

        $startDate = $startDatePeriod;
        $endDate = $endDatePeriod;

        $employees = Employee::query()->whereHas('salaries', function ($query) use ($startDate, $endDate) {
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

        // if ($staffOnly) {
        //     $employees->where('type', 'staff');
        // }
        if (!$haveStaffPermission) {
            $employees->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $employees->where('type', 'staff');
            }
        }

        $employees = $employees->orderBy('name', 'ASC')->get()
            ->map(function ($employee) {
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

                $salariesDataCount = collect($employee->salaries)->count();
                if ($salariesDataCount > 0) {
                    $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                        return $salary->items;
                    });
                    $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                    $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                    $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                    $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
                    $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                    $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                }

                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $employee->transaction_id = $transactionId;
                $employee->transfer_type = "BCA";
                $employee->benificiary_id = "";
                $employee->credited_account = $employee->name;
                $employee->bank_account_number = $employee->bankAccounts[0]->account_number ?? "";
                $employee->amount = $total;
                $employee->nip = "";
                $employee->remark = "";
                $employee->benificiary_email_address = "";
                $employee->receiver_swift_code = "";
                $employee->receiver_cust_type = "";
                $employee->receiver_cust_residence = "";

                // $employee->basic_salary = $basicSalary;
                // $employee->position_allowance = $positionAllowance;
                // $employee->attendance_allowance = $attendanceAllowance;
                // $employee->loan = $currentMonthLoans;
                // $employee->total_loan = $totalLoan;
                // $employee->total_paid_loan = $totalPaidLoan;
                // $employee->loan_balance = $remainingLoan;
                // $employee->excess_leave = $excessLeave;
                // $employee->total = $total;
                // $employee->bank_account_number = $bankAccountNumber;

                return $employee;
            });

        // return $employees;

        return Excel::download(new MultiPayrollTemplateExport($startDatePeriod, $endDatePeriod, $staffOnly, $haveStaffPermission, $companyId, $employees), $fileName);
    }

    public function multiPayrollTemplateDailyAerplus(Request $request)
    {
        $startDate = $request->query('start_date_period');
        $endDate = $request->query('end_date_period');

        $employees = DailySalary::with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'bankAccounts']);
        }])
            ->where('type', 'aerplus')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get()->each(function (DailySalary $dailySalary) {
                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $total = $dailySalary->take_home_pay;

                $dailySalary->transaction_id = $transactionId;
                $dailySalary->transfer_type = "BCA";
                $dailySalary->benificiary_id = "";
                $dailySalary->credited_account = $dailySalary->employee->name;
                $dailySalary->bank_account_number = $dailySalary->employee->bankAccounts[0]->account_number ?? "";
                $dailySalary->amount = $total;
                $dailySalary->nip = "";
                $dailySalary->remark = "";
                $dailySalary->benificiary_email_address = "";
                $dailySalary->receiver_swift_code = "";
                $dailySalary->receiver_cust_type = "";
                $dailySalary->receiver_cust_residence = "";

                return $dailySalary;
            });

        return $this->exportMultiPayrollTemplateDaily($request, $employees);
    }


    public function transactionTxtDailyAerplus(Request $request)
    {
        $startDate = $request->query('start_date_period');
        $endDate = $request->query('end_date_period');

        $employees = DailySalary::with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'bankAccounts']);
        }])
            ->where('type', 'aerplus')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get()->each(function (DailySalary $dailySalary) {
                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $total = $dailySalary->take_home_pay;

                $dailySalary->transaction_id = $transactionId;
                $dailySalary->transfer_type = "BCA";
                $dailySalary->benificiary_id = "";
                $dailySalary->credited_account = $dailySalary->employee->name;
                $dailySalary->bank_account_number = $dailySalary->employee->bankAccounts[0]->account_number ?? "";
                $dailySalary->amount = $total;
                $dailySalary->nip = "";
                $dailySalary->remark = "";
                $dailySalary->benificiary_email_address = "";
                $dailySalary->receiver_swift_code = "";
                $dailySalary->receiver_cust_type = "";
                $dailySalary->receiver_cust_residence = "";

                return $dailySalary;
            });

        // return $employees;

        return $this->exportTransactionTxtDaily($request, $employees);
    }

    /**
     * Export report monthly
     */
    public function exportTransactionTxt(Request $request)
    {
        $startDatePeriod = $request->query('start_date_period');
        $endDatePeriod = $request->query('end_date_period');
        // $staffOnly = $request->query('staffonly');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $companyId = request()->query('company_id');
        $staffOnly = $staffOnly == 'true' ? true : false;

        $headerId = $request->query('header_id') ?? "";
        $effectiveDate = $request->query('effective_date') ?? "";
        $effectiveTime = $request->query('effective_time') ?? "";
        $remark = $request->query('remark') ?? "";

        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $company = Company::with(['businessType'])->find($companyId);
        // return $company;
        $fileName = 'HONOR ' . $startDatePeriod . ' - ' . $endDatePeriod . ' ' . ($company->name ?? '') . '.txt';

        $startDate = $startDatePeriod;
        $endDate = $endDatePeriod;

        $employees = Employee::query()->whereHas('salaries', function ($query) use ($startDate, $endDate) {
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

        // if ($staffOnly) {
        //     $employees->where('type', 'staff');
        // }
        if (!$haveStaffPermission) {
            $employees->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $employees->where('type', 'staff');
            }
        }

        $employees = $employees->orderBy('name', 'ASC')->get()
            ->map(function ($employee) {
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

                $salariesDataCount = collect($employee->salaries)->count();
                if ($salariesDataCount > 0) {
                    $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                        return $salary->items;
                    });
                    $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                    $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                    $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                    $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
                    $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                    $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
                }

                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $employee->transaction_id = $transactionId;
                $employee->transfer_type = "BCA";
                $employee->benificiary_id = "";
                $employee->credited_account = $employee->name;
                $employee->bank_account_number = $employee->bankAccounts[0]->account_number ?? "";
                $employee->amount = $total;
                $employee->nip = "";
                $employee->remark = "";
                $employee->benificiary_email_address = "";
                $employee->receiver_swift_code = "";
                $employee->receiver_cust_type = "";
                $employee->receiver_cust_residence = "";

                return $employee;
            })->filter(function ($employee) use ($companyId) {
                return $employee?->office?->division?->company?->id == $companyId;
            })->values()->all();

        // return $employees;

        $content = "";
        $usedRemarkNames = [];
        $columnSeparator = "|";


        // header and content identifier
        $content .= "0";
        $content .= $columnSeparator;
        // Transaction Type
        $content .= "PY";
        $content .= $columnSeparator;
        // Corporate ID
        $content .= $company->corporate_id;
        $content .= $columnSeparator;
        // Company Code
        // $cleanCompanyCode = preg_replace("/[^a-zA-Z0-9\s]/", "", $company->company_code);
        $cleanCompanyCode = $company->company_code;
        $explodedCompanyCode = explode("/", $company->company_code);
        if (isset($explodedCompanyCode[0]) && isset($explodedCompanyCode[1])) {
            $cleanCompanyCode = $explodedCompanyCode[0] . $explodedCompanyCode[1];
        }
        $content .= $cleanCompanyCode;
        $content .= $columnSeparator;
        // !Header ID
        $content .= str_pad($headerId, 8, '0', STR_PAD_LEFT);
        $content .= $columnSeparator;
        // !Effective Date
        $content .= Carbon::parse($effectiveDate)->format('Ymd');
        $content .= $columnSeparator;
        // !Effective Time
        $content .= $effectiveTime;
        $content .= $columnSeparator;
        // Debited Account
        $content .= $company->debited_account;
        $content .= $columnSeparator;
        // 
        $content .= str_pad(count($employees), 5, '0', STR_PAD_LEFT);
        $content .= $columnSeparator;
        // Business Type
        $content .= str_pad($company->businessType->value ?? "", 2, '0', STR_PAD_LEFT);
        $content .= $columnSeparator;
        // !Remark
        $content .= $remark;
        $content .= $columnSeparator;

        $content .= "\n";

        collect($employees)->each(function ($employee) use (&$content, &$usedRemarkNames, $endDate, $columnSeparator) {
            $newLine = "\n";
            // No	Transaction ID	Transfer Type 	Beneficiary ID	Credited Account	Receiver Name	Amount	NIP	Remark	Beneficiary email address	Receiver Swift Code	Receiver Cust Type	Receiver Cust Residence
            // 1|0000000024FEB24001|BCA||4800341057|434500|434500.00||HONMIGADUL1622||||
            $content .= '1';
            $content .= $columnSeparator;
            // Transaction ID
            $content .= ("00000000" . $employee->transaction_id);
            $content .= $columnSeparator;
            // Transfer Type
            $content .= $employee->transfer_type;
            $content .= $columnSeparator;
            // Benificiary ID
            $content .= $employee->benificiary_id;
            $content .= $columnSeparator;
            // Bank Account Number
            $content .= $employee->bank_account_number;
            $content .= $columnSeparator;
            // Amount
            $amount = $employee->amount > 0 ? $employee->amount : 0;
            $content .= $amount;
            $content .= $columnSeparator;
            // Amount 2
            $content .= number_format($amount, 2, '.', '');
            $content .= $columnSeparator;
            // NIP
            $content .= $employee->nip;
            $content .= $columnSeparator;
            // Remark
            $tryedNames = [];
            $joinedName = preg_replace('/\PL/u', '', str_replace(" ", "", $employee->credited_account ?? ""));
            $remark = "";
            if (strlen($joinedName) > 4) {
                $willRemoveCharIndex = 3;
                $tryIteration = 1;
                do {
                    $remark = substr($joinedName, 0, 4);
                    $joinedName = substr_replace($joinedName, "", $willRemoveCharIndex, 1);
                    array_push($tryedNames, $willRemoveCharIndex . ' - ' . $joinedName);
                    if ($willRemoveCharIndex > 10) {
                        break;
                    }
                    $tryIteration++;
                } while (
                    in_array($remark, $usedRemarkNames)
                );
            } else {
                $remark = substr($joinedName, 0, 4);
            }
            array_push($usedRemarkNames, $remark);

            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
            $remark = "HON" . strtoupper($remark) . ($months[(\Carbon\Carbon::parse($endDate)->month) - 1]) . \Carbon\Carbon::parse($endDate)->format('y');
            $content .= $remark;
            $content .= $columnSeparator;
            // Beneficiary email address
            $content .= $employee->benificiary_email_address;
            $content .= $columnSeparator;
            // Receiver swift code
            $content .= $employee->receiver_swift_code;
            $content .= $columnSeparator;
            // Receiver Cust Type
            $content .= $employee->receiver_cust_type;
            $content .= $columnSeparator;
            // Receiver Cust Residence
            $content .= $employee->receiver_cust_residence;

            $content .= $newLine;
        });


        // use headers in order to generate the download
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => strlen($content)
        ];

        // make a response, with the content, a 200 response code and the headers
        return Response::make($content, 200, $headers);
    }

    public function multiPayrollTemplateDailyMagenta(Request $request)
    {
        $startDate = $request->query('start_date_period');
        $endDate = $request->query('end_date_period');

        $employees = DailySalary::with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'bankAccounts']);
        }])
            ->where('type', 'regular')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get()->each(function (DailySalary $dailySalary) {
                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $total = $dailySalary->take_home_pay;

                $dailySalary->transaction_id = $transactionId;
                $dailySalary->transfer_type = "BCA";
                $dailySalary->benificiary_id = "";
                $dailySalary->credited_account = $dailySalary->employee->name;
                $dailySalary->bank_account_number = $dailySalary->employee->bankAccounts[0]->account_number ?? "";
                $dailySalary->amount = $total;
                $dailySalary->nip = "";
                $dailySalary->remark = "";
                $dailySalary->benificiary_email_address = "";
                $dailySalary->receiver_swift_code = "";
                $dailySalary->receiver_cust_type = "";
                $dailySalary->receiver_cust_residence = "";

                return $dailySalary;
            });

        return $this->exportMultiPayrollTemplateDaily($request, $employees);
    }

    public function transactionTxtDailyMagenta(Request $request)
    {
        $startDate = $request->query('start_date_period');
        $endDate = $request->query('end_date_period');

        $employees = DailySalary::with(['employee' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'bankAccounts']);
        }])
            ->where('type', 'regular')
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->get()->each(function (DailySalary $dailySalary) {
                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                $transactionId = Carbon::now()->format('d') . ($months[Carbon::now()->month - 1]) . Carbon::now()->format('y');

                $total = $dailySalary->take_home_pay;

                $dailySalary->transaction_id = $transactionId;
                $dailySalary->transfer_type = "BCA";
                $dailySalary->benificiary_id = "";
                $dailySalary->credited_account = $dailySalary->employee->name;
                $dailySalary->bank_account_number = $dailySalary->employee->bankAccounts[0]->account_number ?? "";
                $dailySalary->amount = $total;
                $dailySalary->nip = "";
                $dailySalary->remark = "";
                $dailySalary->benificiary_email_address = "";
                $dailySalary->receiver_swift_code = "";
                $dailySalary->receiver_cust_type = "";
                $dailySalary->receiver_cust_residence = "";

                return $dailySalary;
            });

        // return $employees;

        return $this->exportTransactionTxtDaily($request, $employees);
    }

    /**
     * Export report monthly
     */
    public function exportMultiPayrollTemplateDaily(Request $request, $employees)
    {
        $startDatePeriod = $request->query('start_date_period');
        $endDatePeriod = $request->query('end_date_period');
        // $staffOnly = $request->query('staffonly');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $companyId = request()->query('company_id');
        $staffOnly = $staffOnly == 'true' ? true : false;

        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $company = Company::find($companyId);
        // return $company;
        $fileName = 'TEMPLATE MULTI PAYROLL HONOR MINGGUAN ' . $startDatePeriod . ' - ' . $endDatePeriod . ' ' . ($company->name ?? '') . '.xlsx';

        $startDate = $startDatePeriod;
        $endDate = $endDatePeriod;

        return Excel::download(new MultiPayrollTemplateDailyExport($startDatePeriod, $endDatePeriod, $staffOnly, $haveStaffPermission, $companyId, $employees), $fileName);
    }

    /**
     * Export report monthly
     */
    private function exportTransactionTxtDaily(Request $request, $employees)
    {
        $startDatePeriod = $request->query('start_date_period');
        $endDatePeriod = $request->query('end_date_period');
        // $staffOnly = $request->query('staffonly');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $companyId = request()->query('company_id');
        $staffOnly = $staffOnly == 'true' ? true : false;

        $headerId = $request->query('header_id') ?? "";
        $effectiveDate = $request->query('effective_date') ?? "";
        $effectiveTime = $request->query('effective_time') ?? "";
        $remark = $request->query('remark') ?? "";

        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $company = Company::with(['businessType'])->find($companyId);
        // return $company;
        $fileName = 'HONOR MINGGUAN' . $startDatePeriod . ' - ' . $endDatePeriod . ' ' . ($company->name ?? '') . '.txt';

        $startDate = $startDatePeriod;
        $endDate = $endDatePeriod;

        $content = "";
        $usedRemarkNames = [];
        $columnSeparator = "|";

        // header and content identifier
        $content .= "0";
        $content .= $columnSeparator;
        // Transaction Type
        $content .= "PY";
        $content .= $columnSeparator;
        // Corporate ID
        $content .= $company->corporate_id;
        $content .= $columnSeparator;
        // Company Code
        // $cleanCompanyCode = preg_replace("/[^a-zA-Z0-9\s]/", "", $company->company_code);
        $cleanCompanyCode = $company->company_code;
        $explodedCompanyCode = explode("/", $company->company_code);
        if (isset($explodedCompanyCode[0]) && isset($explodedCompanyCode[1])) {
            $cleanCompanyCode = $explodedCompanyCode[0] . $explodedCompanyCode[1];
        }
        $content .= $cleanCompanyCode;
        $content .= $columnSeparator;
        // !Header ID
        $content .= str_pad($headerId, 8, '0', STR_PAD_LEFT);
        $content .= $columnSeparator;
        // !Effective Date
        $content .= Carbon::parse($effectiveDate)->format('Ymd');
        $content .= $columnSeparator;
        // !Effective Time
        $content .= $effectiveTime;
        $content .= $columnSeparator;
        // Debited Account
        $content .= $company->debited_account;
        $content .= $columnSeparator;
        // 
        $content .= str_pad(count($employees), 5, '0', STR_PAD_LEFT);
        $content .= $columnSeparator;
        // Business Type
        $content .= str_pad($company->businessType->value ?? "", 2, '0', STR_PAD_LEFT);
        $content .= $columnSeparator;
        // !Remark
        $content .= $remark;
        $content .= $columnSeparator;

        $content .= "\n";

        collect($employees)->each(function ($employee) use (&$content, &$usedRemarkNames, $endDate, $columnSeparator) {
            $newLine = "\n";
            // No	Transaction ID	Transfer Type 	Beneficiary ID	Credited Account	Receiver Name	Amount	NIP	Remark	Beneficiary email address	Receiver Swift Code	Receiver Cust Type	Receiver Cust Residence
            // 1|0000000024FEB24001|BCA||4800341057|434500|434500.00||HONMIGADUL1622||||
            $content .= '1';
            $content .= $columnSeparator;
            // Transaction ID
            $content .= ("00000000" . $employee->transaction_id);
            $content .= $columnSeparator;
            // Transfer Type
            $content .= $employee->transfer_type;
            $content .= $columnSeparator;
            // Benificiary ID
            $content .= $employee->benificiary_id;
            $content .= $columnSeparator;
            // Bank Account Number
            $content .= $employee->bank_account_number;
            $content .= $columnSeparator;
            // Amount
            $amount = $employee->amount > 0 ? $employee->amount : 0;
            $content .= $amount;
            $content .= $columnSeparator;
            // Amount 2
            $content .= number_format($amount, 2, '.', '');
            $content .= $columnSeparator;
            // NIP
            $content .= $employee->nip;
            $content .= $columnSeparator;
            // Remark
            $tryedNames = [];
            $joinedName = preg_replace('/\PL/u', '', str_replace(" ", "", $employee->credited_account ?? ""));
            $remark = "";
            if (strlen($joinedName) > 4) {
                $willRemoveCharIndex = 3;
                $tryIteration = 1;
                do {
                    $remark = substr($joinedName, 0, 4);
                    $joinedName = substr_replace($joinedName, "", $willRemoveCharIndex, 1);
                    array_push($tryedNames, $willRemoveCharIndex . ' - ' . $joinedName);
                    if ($willRemoveCharIndex > 10) {
                        break;
                    }
                    $tryIteration++;
                } while (
                    in_array($remark, $usedRemarkNames)
                );
            } else {
                $remark = substr($joinedName, 0, 4);
            }
            array_push($usedRemarkNames, $remark);

            $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
            $remark = "HONMIG" . strtoupper($remark) . ($months[(\Carbon\Carbon::parse($endDate)->month) - 1]) . \Carbon\Carbon::parse($endDate)->format('y');
            $content .= $remark;
            $content .= $columnSeparator;
            // Beneficiary email address
            $content .= $employee->benificiary_email_address;
            $content .= $columnSeparator;
            // Receiver swift code
            $content .= $employee->receiver_swift_code;
            $content .= $columnSeparator;
            // Receiver Cust Type
            $content .= $employee->receiver_cust_type;
            $content .= $columnSeparator;
            // Receiver Cust Residence
            $content .= $employee->receiver_cust_residence;

            $content .= $newLine;
        });


        // use headers in order to generate the download
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => strlen($content)
        ];

        // make a response, with the content, a 200 response code and the headers
        return Response::make($content, 200, $headers);
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
}
