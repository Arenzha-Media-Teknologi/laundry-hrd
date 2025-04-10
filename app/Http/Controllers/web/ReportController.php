<?php

namespace App\Http\Controllers\web;

use App\Exports\reports\attendances\AbsencesExport;
use App\Exports\reports\attendances\AttendancesAllReportExport;
use App\Exports\reports\attendances\AttendancesByEmployeeReportExport;
use App\Exports\reports\employees\EmployeesReportExport;
use App\Exports\reports\insurances\BpjsKetenagakerjaanExport;
use App\Exports\reports\insurances\BpjsMandiriExport;
use App\Exports\reports\insurances\InsurancesListExport;
use App\Exports\reports\insurances\PrivateInsurancesExport;
use App\Exports\reports\salaries\DepositExport;
use App\Exports\reports\salaries\LeavePayrollExport;
use App\Exports\reports\salaries\MonthlyReportExport;
use App\Exports\reports\salaries\ThrReportExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EventCalendar;
use App\Models\PrivateInsurance;
use App\Models\SalaryComponent;
use App\Models\SalaryDeposit;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $privateInsurances = PrivateInsurance::all();
        return view('reports.index', [
            'private_insurances' => $privateInsurances,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employees()
    {
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $startDate = request()->query('start_date');
        $endDate = request()->query('end_date');
        $status = request()->query('status');
        if (!isset($startDate) || !isset($endDate)) {
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-t');
        }

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        // $divisions = Division::all();
        // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate, $status) {
            // !FATAL: NEED ADD INACTIVE PARAMETER
            $q->with(['office', 'credential'])->where('start_work_date', '>=', $startDate);
            if (isset($status)) {
                $q->where('active', $status);
            }
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get();

        $divisionsOptions = Division::all();

        // return [$startDate, $endDate];
        // return $divisions;
        return view('reports.employees.employees', [
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employeesExport()
    {
        try {
            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');
            $status = request()->query('status');
            if (!isset($startDate) || !isset($endDate)) {
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-t');
            }

            $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
            // $divisions = Division::all();
            // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate, $status) {
                // !FATAL: NEED ADD INACTIVE PARAMETER
                $q->with(['activeCareer.jobTitle.designation.department', 'salaryComponents', 'bankAccounts'])
                    ->where('start_work_date', '>=', $startDate);
                if (isset($status)) {
                    $q->where('active', $status);
                }
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }


            $divisions = $divisionsQuery
                ->get();

            $company = Company::find($companyId);

            return Excel::download(new EmployeesReportExport($divisions, $startDate, $endDate, $company), 'Daftar Karyawan ' . $startDate . ' - ' . $endDate . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Leaves
     */
    public function leaves()
    {
        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        $divisions = Division::all();
        $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle', 'leave'])->get();
        return view('reports.leaves.leaves', [
            'companies' => $companies,
            'employees' => $employees,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Leaves
     */
    public function leavesByEmployee()
    {
        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        $divisions = Division::all();
        $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle', 'leave'])->get();
        return view('reports.leaves.leaves-by-employee', [
            'companies' => $companies,
            'employees' => $employees,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Monthly Salary
     */
    public function monthlySalaries()
    {
        // return Division::with(['employees'])->get();

        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $currentDate = date('Y-m-d');
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;
        // $startMonth = request()->query('start_month');
        // $endMonth = request()->query('end_month');

        // $salaryStartDayPeriod = 26;
        // if (!isset($startMonth) || !isset($endMonth)) {
        //     $startMonth = date('Y-m');
        //     $endMonth = date('Y-m');
        // }

        // if ($startMonth > $endMonth) {
        //     return response()->json([
        //         'message' => 'periode tidak valid',
        //     ]);
        // }

        // $startDate = $startMonth . '-' . (sprintf('%02d', $salaryStartDayPeriod));
        // $endDate = $endMonth . '-' . (sprintf('%02d', $salaryStartDayPeriod - 1));

        $month = request()->query('month');

        $salaryStartDayPeriod = 26;
        if (!isset($month)) {
            $month = date('Y-m');
        }

        $carbonDate = Carbon::parse($month . '-' . $salaryStartDayPeriod);

        $startDate = $carbonDate->subMonth()->toDateString();
        $endDate = $carbonDate->addMonth()->subDay()->toDateString();

        // $staffOnly = false;
        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        // return [$companyId, $startDate, $endDate];

        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate, $staffOnly, $haveStaffPermission) {
            $q->whereHas('salaries', function ($query) use ($startDate, $endDate) {
                // $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
                $query->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
            });

            // if ($staffOnly == 'true') {
            //     $q->where('type', 'staff');
            // }
            if (!$haveStaffPermission) {
                $q->where('type', '!=', 'staff');
            } else {
                if ($staffOnly) {
                    $q->where('type', 'staff');
                }
            }

            $q->with([
                'activeCareer.jobTitle',
                'bankAccounts',
                'loans' => function ($q) use ($endDate) {
                    $q->with(['items' => function ($itemQuery) use ($endDate) {
                        $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
                    }]);
                },
                'salaries' => function ($query) use ($startDate, $endDate) {
                    // $query->where('start_date', $startDate)->where('end_date', $endDate);
                    $query->with(['items'])->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
                }
            ]);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get()
            ->each(function ($division) use ($startDate, $endDate) {
                collect($division->employees)->each(function ($employee) use ($startDate, $endDate) {
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
                    if ($salariesDataCount > 0) {
                        $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                            return $salary->items;
                        });
                        $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                        $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                        $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                        $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->where('type', 'deduction')->sum('amount');
                        $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                        $total = ($basicSalary + $positionAllowance + $attendanceAllowance + $currentMonthSingleLoan) - ($currentMonthLoans + $excessLeave);
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

                    // return $employee;
                });

                $properties = ['basic_salary', 'position_allowance', 'attendance_allowance', 'bruto_salary', 'total_loan', 'current_month_loan', 'loan', 'remaining_loan', 'total_paid_loan', 'excess_leave', 'total'];

                // $totalBasicSalary = collect($division->employees)->sum('basic_salary');
                // $totalPositionAllowance = collect($division->employees)->sum('position_allowance');
                // $totalAttendanceAllowance = collect($division->employees)->sum('attendance_allowance');
                // $totalBrutoSalary = collect($division->employees)->sum('bruto_salary');
                // $totalTotalLoan = collect($division->employees)->sum('total_loan');
                // $totalCurrentMonthLoan = collect($division->employees)->sum('current_month_loan');
                // $totalLoan = collect($division->employees)->sum('loan');
                // $totalRemainingLoan = collect($division->employees)->sum('remaining_loan');
                // $totalPaidLoan = collect($division->employees)->sum('total_paid_loan');
                // $totalExcessLeave = collect($division->employees)->sum('excess_leave');
                // $totalNetto = collect($division->employees)->sum('total');

                $subtotals = [];

                foreach ($properties as $property) {
                    $total = collect($division->employees)->sum($property);
                    // array_push($subtotals, [
                    //     $property => $total,
                    // ]);
                    $subtotals[$property] = $total;
                }

                $division->subtotal = $subtotals;


                // $division->subtotal = [
                //     'basic_salary' => $totalBasicSalary,
                //     'position_allowance' => $totalPositionAllowance,
                //     'attendance_allowance' => $totalAttendanceAllowance,
                // ];
            });

        $properties = ['basic_salary', 'position_allowance', 'attendance_allowance', 'bruto_salary', 'total_loan', 'current_month_loan', 'loan', 'remaining_loan', 'total_paid_loan', 'excess_leave', 'total'];

        $grandtotals = [];

        foreach ($properties as $property) {
            $total = collect($divisions)->sum(function ($division) use ($property) {
                return $division->subtotal[$property] ?? 0;
            });
            // array_push($subtotals, [
            //     $property => $total,
            // ]);
            $grandtotals[$property] = $total;
        }


        // return $divisions;

        // return $employees;

        $companies = Company::all();
        // $divisions = Division::all();

        // return $employees;
        $divisionsOptions = Division::all();

        return view('reports.salaries.monthly-salaries', [
            'salary_components' => $salaryComponents,
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'grand_totals' => $grandtotals,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'month' => $month,
                'staff_only' => $staffOnly,
            ],
            'have_staff_permission' => $haveStaffPermission,
        ]);
    }

    /**
     * Monthly Salary
     */
    public function monthlySalariesExport()
    {
        // return Division::with(['employees'])->get();
        try {
            $salaryComponents = SalaryComponent::with(['employees'])->get();
            $currentDate = date('Y-m-d');
            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $staffOnly = request()->query('staff_only') ?? 'false';
            $staffOnly = $staffOnly == 'true' ? true : false;

            $month = request()->query('month');

            $salaryStartDayPeriod = 26;
            if (!isset($month)) {
                $month = date('Y-m');
            }

            $carbonDate = Carbon::parse($month . '-' . $salaryStartDayPeriod);

            $startDate = $carbonDate->subMonth()->toDateString();
            $endDate = $carbonDate->addMonth()->subDay()->toDateString();

            $user = Auth::user();
            $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

            // return [$companyId, $startDate, $endDate];

            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate, $staffOnly, $haveStaffPermission) {
                $q->whereHas('salaries', function ($query) use ($startDate, $endDate) {
                    // $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
                    $query->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
                });

                if (!$haveStaffPermission) {
                    $q->where('type', '!=', 'staff');
                } else {
                    if ($staffOnly) {
                        $q->where('type', 'staff');
                    }
                }

                $q->with([
                    'activeCareer.jobTitle',
                    'bankAccounts',
                    'loans' => function ($q) use ($endDate) {
                        $q->with(['items' => function ($itemQuery) use ($endDate) {
                            $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
                        }]);
                    },
                    'salaries' => function ($query) use ($startDate, $endDate) {
                        // $query->where('start_date', $startDate)->where('end_date', $endDate);
                        $query->with(['items'])->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
                    }
                ]);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get()
                ->each(function ($division) use ($startDate, $endDate) {
                    collect($division->employees)->each(function ($employee) use ($startDate, $endDate) {
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
                        if ($salariesDataCount > 0) {
                            $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                                return $salary->items;
                            });
                            $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
                            $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
                            $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
                            $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->where('type', 'deduction')->sum('amount');
                            $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                            $total = ($basicSalary + $positionAllowance + $attendanceAllowance + $currentMonthSingleLoan) - ($currentMonthLoans + $excessLeave);
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

                        // return $employee;
                    });

                    $properties = ['basic_salary', 'position_allowance', 'attendance_allowance', 'bruto_salary', 'total_loan', 'current_month_loan', 'loan', 'remaining_loan', 'total_paid_loan', 'excess_leave', 'total'];

                    $subtotals = [];

                    foreach ($properties as $property) {
                        $total = collect($division->employees)->sum($property);
                        // array_push($subtotals, [
                        //     $property => $total,
                        // ]);
                        $subtotals[$property] = $total;
                    }

                    $division->subtotal = $subtotals;
                });

            $properties = ['basic_salary', 'position_allowance', 'attendance_allowance', 'bruto_salary', 'total_loan', 'current_month_loan', 'loan', 'remaining_loan', 'total_paid_loan', 'excess_leave', 'total'];

            $grandtotals = [];

            foreach ($properties as $property) {
                $total = collect($divisions)->sum(function ($division) use ($property) {
                    return $division->subtotal[$property] ?? 0;
                });
                // array_push($subtotals, [
                //     $property => $total,
                // ]);
                $grandtotals[$property] = $total;
            }

            $company = Company::find($companyId);

            // return $grandtotals;

            return Excel::download(new MonthlyReportExport($divisions, $startDate, $endDate, $company, $grandtotals), 'Laporan Honor Periode ' . $startDate . ' sd ' . $endDate . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * THR
     */
    public function thr()
    {
        // return Division::with(['employees'])->get();

        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $currentDate = date('Y-m-d');
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        // $startMonth = request()->query('start_month');
        // $endMonth = request()->query('end_month');

        // $salaryStartDayPeriod = 26;
        // if (!isset($startMonth) || !isset($endMonth)) {
        //     $startMonth = date('Y-m');
        //     $endMonth = date('Y-m');
        // }

        // if ($startMonth > $endMonth) {
        //     return response()->json([
        //         'message' => 'periode tidak valid',
        //     ]);
        // }

        // $startDate = $startMonth . '-' . (sprintf('%02d', $salaryStartDayPeriod));
        // $endDate = $endMonth . '-' . (sprintf('%02d', $salaryStartDayPeriod - 1));

        $month = request()->query('month');
        $year = request()->query('year');

        $salaryStartDayPeriod = 26;
        if (!isset($month)) {
            $month = date('Y-m');
        }

        if (!isset($year)) {
            $year = date('Y');
        }

        $carbonDate = Carbon::parse($month . '-' . $salaryStartDayPeriod);

        $startDate = $carbonDate->subMonth()->toDateString();
        $endDate = $carbonDate->addMonth()->subDay()->toDateString();

        // return [$companyId, $startDate, $endDate];

        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate) {
            $q->whereHas('salaries', function ($query) use ($startDate, $endDate) {
                // $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
                $query->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
            })
                ->with([
                    'activeCareer.jobTitle',
                    'bankAccounts',
                    'loans' => function ($q) use ($endDate) {
                        $q->with(['items' => function ($itemQuery) use ($endDate) {
                            $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
                        }]);
                    },
                    'salaries' => function ($query) use ($startDate, $endDate) {
                        // $query->where('start_date', $startDate)->where('end_date', $endDate);
                        $query->with(['items'])->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
                    }
                ]);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get()
            ->each(function ($division) use ($startDate, $endDate) {
                collect($division->employees)->each(function ($employee) use ($startDate, $endDate) {
                    $thr = 0;

                    $defaultBankAccount = collect($employee->bankAccounts)->where('default', 1)->first();
                    if ($defaultBankAccount !== null) {
                        $bankAccountNumber = $defaultBankAccount['account_number'];
                    }

                    $salariesDataCount = collect($employee->salaries)->count();
                    if ($salariesDataCount > 0) {
                        $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                            return $salary->items;
                        });
                        $thr = $salaryItems->where('salary_type', 'thr')->sum('amount');
                    }

                    // THR
                    $employee->thr = $thr;
                });

                $properties = ['thr'];

                $subtotals = [];

                foreach ($properties as $property) {
                    $total = collect($division->employees)->sum($property);
                    $subtotals[$property] = $total;
                }

                $division->subtotal = $subtotals;
            });

        $properties = ['thr'];

        $grandtotals = [];

        foreach ($properties as $property) {
            $total = collect($divisions)->sum(function ($division) use ($property) {
                return $division->subtotal[$property] ?? 0;
            });
            // array_push($subtotals, [
            //     $property => $total,
            // ]);
            $grandtotals[$property] = $total;
        }

        $companies = Company::all();

        $divisionsOptions = Division::all();

        return view('reports.salaries.thr', [
            'salary_components' => $salaryComponents,
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'month' => $month,
                'year' => $year,
            ]
        ]);
    }

    /**
     * THR
     */
    public function thrV2()
    {
        // return Division::with(['employees'])->get();

        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $currentDate = date('Y-m-d');
        $companyId = request()->query('company');
        $divisionId = request()->query('division');

        $month = request()->query('month');
        $year = request()->query('year') ?? date('Y');

        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($year) {
            $q->with([
                'salaryComponents' => function ($q) use ($year) {
                    $q->whereRaw('employee_salary_component.effective_date = ' . '"' . $year . '-01-01"')->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
                },
                'activeCareer.jobTitle',
                'bankAccounts',
            ]);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get()
            ->each(function ($division) use ($year) {
                collect($division->employees)->each(function ($employee) use ($year) {
                    $salaryComponent = collect($employee->salaryComponents)->where('salary_type', 'thr')->filter(function ($salaryComponent) use ($year) {
                        return $salaryComponent->pivot->effective_date == $year . '-01-01';
                    })->first();

                    // THR
                    $employee->thr = $salaryComponent->pivot->amount ?? 0;
                });

                $properties = ['thr'];

                $subtotals = [];

                foreach ($properties as $property) {
                    $total = collect($division->employees)->sum($property);
                    $subtotals[$property] = $total;
                }

                $division->subtotal = $subtotals;
            });

        // return $divisions;

        $properties = ['thr'];

        $grandtotals = [];

        foreach ($properties as $property) {
            $total = collect($divisions)->sum(function ($division) use ($property) {
                return $division->subtotal[$property] ?? 0;
            });
            // array_push($subtotals, [
            //     $property => $total,
            // ]);
            $grandtotals[$property] = $total;
        }

        // return $grandtotals;

        $companies = Company::all();

        $divisionsOptions = Division::all();

        return view('reports.salaries.thr', [
            'salary_components' => $salaryComponents,
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'grand_totals' => $grandtotals,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'month' => $month,
                'year' => $year,
            ]
        ]);
    }

    /**
     * THR
     */
    public function thrExport()
    {
        // return Division::with(['employees'])->get();

        try {
            $salaryComponents = SalaryComponent::with(['employees'])->get();
            $currentDate = date('Y-m-d');
            $companyId = request()->query('company');
            $divisionId = request()->query('division');

            $month = request()->query('month');
            $year = request()->query('year');

            $salaryStartDayPeriod = 26;
            if (!isset($month)) {
                $month = date('Y-m');
            }

            if (!isset($year)) {
                $year = date('Y');
            }

            $carbonDate = Carbon::parse($month . '-' . $salaryStartDayPeriod);

            $startDate = $carbonDate->subMonth()->toDateString();
            $endDate = $carbonDate->addMonth()->subDay()->toDateString();

            // return [$companyId, $startDate, $endDate];

            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('salaries', function ($query) use ($startDate, $endDate) {
                    // $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
                    $query->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
                })
                    ->with([
                        'activeCareer.jobTitle',
                        'bankAccounts',
                        'loans' => function ($q) use ($endDate) {
                            $q->with(['items' => function ($itemQuery) use ($endDate) {
                                $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
                            }]);
                        },
                        'salaries' => function ($query) use ($startDate, $endDate) {
                            // $query->where('start_date', $startDate)->where('end_date', $endDate);
                            $query->with(['items'])->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate);
                        }
                    ]);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get()
                ->each(function ($division) use ($startDate, $endDate) {
                    collect($division->employees)->each(function ($employee) use ($startDate, $endDate) {
                        $thr = 0;

                        $defaultBankAccount = collect($employee->bankAccounts)->where('default', 1)->first();
                        if ($defaultBankAccount !== null) {
                            $bankAccountNumber = $defaultBankAccount['account_number'];
                        }

                        $salariesDataCount = collect($employee->salaries)->count();
                        if ($salariesDataCount > 0) {
                            $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
                                return $salary->items;
                            });
                            $thr = $salaryItems->where('salary_type', 'thr')->sum('amount');
                        }

                        // THR
                        $employee->thr = $thr;
                    });

                    $properties = ['thr'];

                    $subtotals = [];

                    foreach ($properties as $property) {
                        $total = collect($division->employees)->sum($property);
                        $subtotals[$property] = $total;
                    }

                    $division->subtotal = $subtotals;
                });

            $properties = ['thr'];

            $grandtotals = [];

            foreach ($properties as $property) {
                $total = collect($divisions)->sum(function ($division) use ($property) {
                    return $division->subtotal[$property] ?? 0;
                });
                // array_push($subtotals, [
                //     $property => $total,
                // ]);
                $grandtotals[$property] = $total;
            }

            $company = Company::find($companyId);

            return Excel::download(new ThrReportExport($divisions, $startDate, $endDate, $company, $grandtotals, $year), 'Laporan THR Periode ' . $year . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * THR
     */
    public function thrExportV2()
    {
        // return Division::with(['employees'])->get();

        try {
            $salaryComponents = SalaryComponent::with(['employees'])->get();
            $currentDate = date('Y-m-d');
            $companyId = request()->query('company');
            $divisionId = request()->query('division');

            $month = request()->query('month');
            $year = request()->query('year') ?? date('Y');

            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($year) {
                $q->with([
                    'salaryComponents' => function ($q) use ($year) {
                        $q->whereRaw('employee_salary_component.effective_date = ' . '"' . $year . '-01-01"')->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
                    },
                    'activeCareer.jobTitle',
                    'bankAccounts',
                ]);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get()
                ->each(function ($division) use ($year) {
                    collect($division->employees)->each(function ($employee) use ($year) {
                        $salaryComponent = collect($employee->salaryComponents)->where('salary_type', 'thr')->filter(function ($salaryComponent) use ($year) {
                            return $salaryComponent->pivot->effective_date == $year . '-01-01';
                        })->first();

                        // THR
                        $employee->thr = $salaryComponent->pivot->amount ?? 0;
                    });

                    $properties = ['thr'];

                    $subtotals = [];

                    foreach ($properties as $property) {
                        $total = collect($division->employees)->sum($property);
                        $subtotals[$property] = $total;
                    }

                    $division->subtotal = $subtotals;
                });

            // return $divisions;

            $properties = ['thr'];

            $grandtotals = [];

            foreach ($properties as $property) {
                $total = collect($divisions)->sum(function ($division) use ($property) {
                    return $division->subtotal[$property] ?? 0;
                });
                // array_push($subtotals, [
                //     $property => $total,
                // ]);
                $grandtotals[$property] = $total;
            }

            $company = Company::find($companyId);

            return Excel::download(new ThrReportExport($divisions, $year . '-01-01', date($year . '-12-t'), $company, $grandtotals, $year), 'Laporan THR Periode ' . $year . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * THR
     */
    public function taxes()
    {
        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $currentDate = date('Y-m-d');
        $employees = Employee::with(['salaryComponents' => function ($q) use ($currentDate) {
            $q->wherePivot('effective_date', '<=', $currentDate)->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
        }, 'office.division', 'activeCareer.jobTitle'])
            ->get()
            ->each(function ($employee) {
                // Gaji Pokok, Harian, Tunjangan, Lembur,
                $salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];

                foreach ($salaryComponentTypes as $salaryComponentType) {
                    $salaryComponent = collect($employee->salaryComponents)->where('salary_type', $salaryComponentType)->first();

                    $salaryComponentValue = 0;
                    $salaryComponentCoefficient = 1;

                    if (isset($salaryComponent)) {
                        $salaryComponentValue = $salaryComponent->pivot->amount ?? 0;
                        $salaryComponentCoefficient = $salaryComponent->pivot->coefficient ?? 1;
                    }

                    $employee->{$salaryComponentType . '_value'} = $salaryComponentValue;
                    $employee->{$salaryComponentType . '_coefficient'} = $salaryComponentCoefficient;
                }
            });

        // return $employees;

        $companies = Company::all();
        $divisions = Division::all();

        // return $employees;

        return view('reports.salaries.taxes', [
            'salary_components' => $salaryComponents,
            'employees' => $employees,
            'companies' => $companies,
            'divisions' => $divisions,
        ]);
    }


    /**
     * Attendances
     */
    public function attendances()
    {
        // !FATAL: GET ONLY ONE ATTENDANCE DATA PER DATE
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $startDate = request()->query('start_date');
        $endDate = request()->query('end_date');

        if (!isset($startDate) || !isset($endDate)) {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        $diffDays = Carbon::parse($startDate)->diffInDays($endDate) + 1;

        // return $diffDays;

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        // $divisions = Division::all();
        // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate) {
            $q->with(['leave', 'attendances' => function ($q2) use ($startDate, $endDate) {
                $q2->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');
            }])->where('active', 1);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get()
            ->each(function ($division) use ($diffDays) {
                collect($division->employees)->each(function ($employee) use ($diffDays) {
                    $status = ['hadir', 'sakit', 'izin', 'cuti'];
                    $summary = [];
                    $totalAbsence = $diffDays;
                    $totalLate = collect($employee->attendances)->groupBy('date')->map(function ($attendances, $date) {
                        return collect($attendances)->first();
                    })->values()->where('time_late', '>', 0)->sum('time_late');
                    foreach ($status as $stat) {
                        $total = collect($employee->attendances)->where('status', $stat)->groupBy('date')->count();
                        $totalAbsence -= $total;
                        $summary[$stat] = $total;
                    }
                    $summary['absence'] = $totalAbsence;
                    $summary['total_late'] = $totalLate;
                    $employee->attendance_summary = $summary;
                });
            });

        // return $divisions;
        $divisionsOptions = Division::all();

        return view('reports.attendances.attendances', [
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'total_days' => $diffDays,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    /**
     * Attendances
     */
    public function attendancesExport()
    {
        try {
            // !FATAL: GET ONLY ONE ATTENDANCE DATA PER DATE
            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            if (!isset($startDate) || !isset($endDate)) {
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
            }

            $diffDays = Carbon::parse($startDate)->diffInDays($endDate) + 1;

            // return $diffDays;

            $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
            // $divisions = Division::all();
            // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($startDate, $endDate) {
                $q->with(['leave', 'attendances' => function ($q2) use ($startDate, $endDate) {
                    $q2->whereBetween('date', [$startDate, $endDate])->orderBy('id', 'desc');;
                }])->where('active', 1);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get()
                ->each(function ($division) use ($diffDays) {
                    collect($division->employees)->each(function ($employee) use ($diffDays) {
                        $status = ['hadir', 'sakit', 'izin', 'cuti'];
                        $summary = [];
                        $totalAbsence = $diffDays;
                        $totalLate = collect($employee->attendances)->groupBy('date')->map(function ($attendances, $date) {
                            return collect($attendances)->first();
                        })->values()->where('time_late', '>', 0)->sum('time_late');
                        foreach ($status as $stat) {
                            $total = collect($employee->attendances)->where('status', $stat)->groupBy('date')->count();
                            $totalAbsence -= $total;
                            $summary[$stat] = $total;
                        }
                        $summary['absence'] = $totalAbsence;
                        $summary['total_late'] = $totalLate;
                        $employee->attendance_summary = $summary;
                    });
                });

            $company = Company::find($companyId);

            return Excel::download(new AttendancesAllReportExport($divisions, $startDate, $endDate, $company, $diffDays), 'Laporan Absensi Periode ' . $startDate . ' sd ' . $endDate . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Attendances
     */
    public function attendancesByEmployee()
    {

        // !FATAL SAISA CUTI

        $employeeId = request()->query('employee');
        $id = $employeeId;
        $startDate = request()->query('start_date');
        $endDate = request()->query('end_date');

        if (!isset($startDate) || !isset($endDate)) {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        $employee = null;
        $finalAttendances = [];

        if (isset($employeeId)) {
            $employee = Employee::with(['activeCareer' => function ($q) {
                $q->with(['jobTitle.designation']);
            }, 'attendances' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])->orderBy('created_at', 'desc');
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
                    'day_status' => null,
                    'attendance' => null,
                    'events' => $events,
                ];

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

                    $workDurationInMinutes = Carbon::parse($attendance->clock_in_time)->diffInMinutes($attendance->clock_out_time);

                    $workDurationInHour = floor($workDurationInMinutes / 60);
                    $remainingWorkDurationInMinutes = $workDurationInMinutes % 60;

                    $workDuration = '';
                    if (isset($attendance->clock_out_time)) {
                        $workDuration = $workDurationInHour . ' Jam ' . $remainingWorkDurationInMinutes . ' Menit';
                    }

                    $item['attendance']['work_duration'] = $workDuration;
                } else {
                    $statistics['na'] += 1;
                }

                if ($workingPatternItem !== null) {
                    $item['day_status'] = $workingPatternItem['day_status'];
                }

                return $item;
            })->all();
        }

        // return $employees;
        // return $finalAttendances;

        $employees = Employee::all();

        return view('reports.attendances.attendances-by-employee', [
            'employees' => $employees,
            'employee' => $employee,
            'attendances' => $finalAttendances,
            'filter' => [
                'employee_id' => $employeeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    /**
     * Attendances
     */
    public function attendancesByEmployeeExport()
    {
        try {
            $employeeId = request()->query('employee');
            $id = $employeeId;
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            if (!isset($startDate) || !isset($endDate)) {
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
            }

            $employee = null;
            $finalAttendances = [];

            if (isset($employeeId)) {
                $employee = Employee::with(['office', 'activeCareer' => function ($q) {
                    $q->with(['jobTitle.designation']);
                }, 'attendances' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])->orderBy('created_at', 'desc');
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
                        'day_status' => null,
                        'attendance' => null,
                        'events' => $events,
                    ];

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

                        $workDurationInMinutes = Carbon::parse($attendance->clock_in_time)->diffInMinutes($attendance->clock_out_time);

                        $workDurationInHour = floor($workDurationInMinutes / 60);
                        $remainingWorkDurationInMinutes = $workDurationInMinutes % 60;

                        $workDuration = '';
                        if (isset($attendance->clock_out_time)) {
                            $workDuration = $workDurationInHour . ' Jam ' . $remainingWorkDurationInMinutes . ' Menit';
                        }

                        $item['attendance']['work_duration'] = $workDuration;
                    } else {
                        $statistics['na'] += 1;
                    }

                    if ($workingPatternItem !== null) {
                        $item['day_status'] = $workingPatternItem['day_status'];
                    }

                    return $item;
                })->all();

                return Excel::download(new AttendancesByEmployeeReportExport($employee, $finalAttendances, $startDate, $endDate), 'Laporan Absensi Periode ' . $startDate . ' sd ' . $endDate . '.xlsx');
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Attendances
     */
    public function absences()
    {

        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $date = request()->query('date');

        if (!isset($date)) {
            $date = date('Y-m-d');
        }

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        // $divisions = Division::all();
        // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($date) {
            // $q->with(['leave', 'attendances' => function ($q2) use ($startDate, $endDate) {
            //     $q2->whereBetween('date', [$startDate, $endDate]);
            // }]);
            $q->whereDoesntHave('attendances', function ($q) use ($date) {
                $q->where('date', $date)->where('status', 'hadir');
            });
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get();

        // return $divisions;

        // return $employees;

        $divisionsOptions = Division::all();

        return view('reports.attendances.absences', [
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'date' => $date,
            ]
        ]);
    }

    /**
     * Attendances
     */
    public function absencesExport()
    {
        try {
            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $date = request()->query('date');

            if (!isset($date)) {
                $date = date('Y-m-d');
            }

            $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
            // $divisions = Division::all();
            // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($date) {
                // $q->with(['leave', 'attendances' => function ($q2) use ($startDate, $endDate) {
                //     $q2->whereBetween('date', [$startDate, $endDate]);
                // }]);
                $q->whereDoesntHave('attendances', function ($q) use ($date) {
                    $q->where('date', $date)->where('status', 'hadir');
                });
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get();

            $company = Company::find($companyId);

            return Excel::download(new AbsencesExport($divisions, $date, $company), 'Laporan Pegawai Absen Tanggal ' . $date . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }


    /**
     * BPJS Ketenagakerjaan
     */
    public function bpjsKetenagakerjaan()
    {
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $year = request()->query('year');

        if (!isset($year)) {
            $year = date('Y');
        }

        $previousYear = ((int) $year) - 1;
        $currentYear = $year;

        // return $diffDays;

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        // $divisions = Division::all();
        // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear) {
            $q->whereHas('bpjs', function ($q) {
                $field = 'ketenagakerjaan_number';
                $q->where($field, '!=', null);
            })->with(['bpjs', 'bpjsValues' => function ($q) use ($previousYear, $currentYear) {
                // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                $q->where('year', $currentYear)->orderBy('id', 'desc');
            }]);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get()
            ->lazy()
            ->each(function ($division) use ($previousYear, $currentYear) {
                collect($division->employees)->each(function ($employee) use ($previousYear, $currentYear) {
                    $previousYearBpjsValue = collect($employee->bpjsValues)->where('year', $previousYear)->first();
                    $currentYearBpjsValue = collect($employee->bpjsValues)->where('year', $currentYear)->first();
                    $employee->previous_year_value = [
                        'jht' => $previousYearBpjsValue->jht ?? 0,
                        'jkk' => $previousYearBpjsValue->jkk ?? 0,
                        'jkm' => $previousYearBpjsValue->jkm ?? 0,
                        'jp' => $previousYearBpjsValue->jp ?? 0,
                    ];
                    $employee->current_year_value = [
                        'jht_payment' => $currentYearBpjsValue->jht_payment ?? '',
                        'jkk_payment' => $currentYearBpjsValue->jkk_payment ?? '',
                        'jkm_payment' => $currentYearBpjsValue->jkm_payment ?? '',
                        'jp_payment' => $currentYearBpjsValue->jp_payment ?? '',
                        'jht' => $currentYearBpjsValue->jht ?? '',
                        'jkk' => $currentYearBpjsValue->jkk ?? '',
                        'jkm' => $currentYearBpjsValue->jkm ?? '',
                        'jp' => $currentYearBpjsValue->jp ?? '',
                    ];
                });

                $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

                $subtotals = [];

                foreach ($properties as $property) {
                    $total = collect($division->employees)->sum(function ($employee) use ($property) {
                        return (int) $employee->current_year_value[$property] ?? 0;
                    });
                    $subtotals[$property] = $total;
                }

                $division->subtotal = $subtotals;
            });

        $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

        $grandtotals = [];

        foreach ($properties as $property) {
            $total = collect($divisions)->sum(function ($division) use ($property) {
                return $division->subtotal[$property] ?? 0;
            });
            // array_push($subtotals, [
            //     $property => $total,
            // ]);
            $grandtotals[$property] = $total;
        }

        $divisionsOptions = Division::all();

        // return $divisions;

        return view('reports.insurances.bpjs-ketenagakerjaan', [
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'grand_totals' => $grandtotals,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'year' => $year,
            ]
        ]);
    }

    /**
     * BPJS Ketenagakerjaan
     */
    public function bpjsKetenagakerjaanExport()
    {
        try {
            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $year = request()->query('year');

            if (!isset($year)) {
                $year = date('Y');
            }

            $previousYear = ((int) $year) - 1;
            $currentYear = $year;

            // return $diffDays;

            $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
            // $divisions = Division::all();
            // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear) {
                $q->whereHas('bpjs', function ($q) {
                    $field = 'ketenagakerjaan_number';
                    $q->where($field, '!=', null);
                })->with(['bpjs', 'bpjsValues' => function ($q) use ($previousYear, $currentYear) {
                    // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                    $q->where('year', $currentYear)->orderBy('id', 'desc');
                }]);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get()
                ->lazy()
                ->each(function ($division) use ($previousYear, $currentYear) {
                    collect($division->employees)->each(function ($employee) use ($previousYear, $currentYear) {
                        $previousYearBpjsValue = collect($employee->bpjsValues)->where('year', $previousYear)->first();
                        $currentYearBpjsValue = collect($employee->bpjsValues)->where('year', $currentYear)->first();
                        $employee->previous_year_value = [
                            'jht' => $previousYearBpjsValue->jht ?? 0,
                            'jkk' => $previousYearBpjsValue->jkk ?? 0,
                            'jkm' => $previousYearBpjsValue->jkm ?? 0,
                            'jp' => $previousYearBpjsValue->jp ?? 0,
                        ];
                        $employee->current_year_value = [
                            'jht_payment' => $currentYearBpjsValue->jht_payment ?? '',
                            'jkk_payment' => $currentYearBpjsValue->jkk_payment ?? '',
                            'jkm_payment' => $currentYearBpjsValue->jkm_payment ?? '',
                            'jp_payment' => $currentYearBpjsValue->jp_payment ?? '',
                            'jht' => $currentYearBpjsValue->jht ?? '',
                            'jkk' => $currentYearBpjsValue->jkk ?? '',
                            'jkm' => $currentYearBpjsValue->jkm ?? '',
                            'jp' => $currentYearBpjsValue->jp ?? '',
                        ];
                    });

                    $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

                    $subtotals = [];

                    foreach ($properties as $property) {
                        $total = collect($division->employees)->sum(function ($employee) use ($property) {
                            return (int) $employee->current_year_value[$property] ?? 0;
                        });
                        $subtotals[$property] = $total;
                    }

                    $division->subtotal = $subtotals;
                });

            $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

            $grandtotals = [];

            foreach ($properties as $property) {
                $total = collect($divisions)->sum(function ($division) use ($property) {
                    return $division->subtotal[$property] ?? 0;
                });

                $grandtotals[$property] = $total;
            }

            $company = Company::find($companyId);

            return Excel::download(new BpjsKetenagakerjaanExport($divisions, $year, $company, $grandtotals), 'Rekap BPJS Ketenagakerjaan - Periode Thn ' . $year . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * BPJS Mandiri
     */
    public function bpjsMandiri()
    {
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $year = request()->query('year');

        if (!isset($year)) {
            $year = date('Y');
        }

        $previousYear = ((int) $year) - 1;
        $currentYear = $year;

        // return $diffDays;

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        // $divisions = Division::all();
        // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear) {
            $q->whereHas('bpjs', function ($q) {
                $field = 'mandiri_number';
                $q->where($field, '!=', null);
            })->with(['bpjs']);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get();

        $divisionsOptions = Division::all();

        return view('reports.insurances.bpjs-mandiri', [
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'year' => $year,
            ]
        ]);
    }

    /**
     * BPJS Mandiri
     */
    public function bpjsMandiriExport()
    {

        // !FATAL: POLICY NUMBER CONVERTED TO NUMBER TYPE
        try {
            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $year = request()->query('year');

            if (!isset($year)) {
                $year = date('Y');
            }

            $previousYear = ((int) $year) - 1;
            $currentYear = $year;

            // return $diffDays;

            $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
            // $divisions = Division::all();
            // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear) {
                $q->whereHas('bpjs', function ($q) {
                    $field = 'mandiri_number';
                    $q->where($field, '!=', null);
                })->with(['bpjs']);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get();

            $company = Company::find($companyId);

            return Excel::download(new BpjsMandiriExport($divisions, $year, $company), 'Rekap BPJS Mandiri - Periode Thn ' . $year . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }


    /**
     * BPJS Mandiri
     */
    public function privateInsurances($id)
    {
        $privateInsurance = PrivateInsurance::findOrFail($id);

        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $year = request()->query('year');

        if (!isset($year)) {
            $year = date('Y');
        }

        $previousYear = ((int) $year) - 1;
        $currentYear = $year;

        // return $diffDays;

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();

        $insuranceId = $id;

        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear, $insuranceId) {
            $q->whereHas('privateInsurances', function ($q) use ($insuranceId) {
                $q->where('private_insurances.id', $insuranceId);
            })->with(['privateInsurances' => function ($q) use ($insuranceId) {
                $q->where('private_insurances.id', $insuranceId);
            }, 'privateInsuranceValues' => function ($q) use ($previousYear, $currentYear) {
                $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
            }]);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get()
            ->lazy()
            ->each(function ($division) use ($previousYear, $currentYear, $privateInsurance) {
                collect($division->employees)->each(function ($employee) use ($previousYear, $currentYear, $privateInsurance) {
                    $previousYearValue = collect($employee->privateInsuranceValues)->where('year', $previousYear)->first();
                    $currentYearValue = collect($employee->privateInsuranceValues)->where('year', $currentYear)->first();

                    $currentPrivateInsurance = collect($employee->privateInsurances)->first();

                    $employee->current_private_insurance = $currentPrivateInsurance;

                    $employee->previous_year_value = [
                        'total_premi' => $previousYearValue->total_premi ?? 0,
                        'kesehatan' => $previousYearValue->kesehatan ?? 0,
                        'nilai_tabungan' => $previousYearValue->nilai_tabungan ?? 0,
                    ];
                    $employee->current_year_value = [
                        'total_premi' => $currentYearValue->total_premi ?? 0,
                        'kesehatan' => $currentYearValue->kesehatan ?? 0,
                        'premi_tabungan' => $privateInsurance->premi_tabungan ?? 0,
                        'nilai_tabungan' => $currentYearValue->nilai_tabungan ?? 0,
                        'premi_kematian' => $privateInsurance->premi_kematian ?? 0,
                    ];
                });

                $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];

                $subtotals = [];

                foreach ($properties as $property) {
                    $total = collect($division->employees)->sum(function ($employee) use ($property) {
                        return $employee->current_year_value[$property] ?? 0;
                    });
                    $subtotals[$property] = $total;
                }

                $division->subtotal = $subtotals;
            });

        $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];

        $grandtotals = [];

        foreach ($properties as $property) {
            $total = collect($divisions)->sum(function ($division) use ($property) {
                return $division->subtotal[$property] ?? 0;
            });
            // array_push($subtotals, [
            //     $property => $total,
            // ]);
            $grandtotals[$property] = $total;
        }

        // return $employees;
        $divisionsOptions = Division::all();

        return view('reports.insurances.privates', [
            'private_insurance' => $privateInsurance,
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'grand_totals' => $grandtotals,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'year' => $year,
            ]
        ]);
    }

    public function privateInsurancesExport($id)
    {
        try {
            $privateInsurance = PrivateInsurance::findOrFail($id);

            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $year = request()->query('year');

            if (!isset($year)) {
                $year = date('Y');
            }

            $previousYear = ((int) $year) - 1;
            $currentYear = $year;

            // return $diffDays;

            $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();

            $insuranceId = $id;

            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear, $insuranceId) {
                $q->whereHas('privateInsurances', function ($q) use ($insuranceId) {
                    $q->where('private_insurances.id', $insuranceId);
                })->with(['privateInsurances' => function ($q) use ($insuranceId) {
                    $q->where('private_insurances.id', $insuranceId);
                }, 'privateInsuranceValues' => function ($q) use ($previousYear, $currentYear) {
                    $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                }]);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get()
                ->lazy()
                ->each(function ($division) use ($previousYear, $currentYear, $privateInsurance) {
                    collect($division->employees)->each(function ($employee) use ($previousYear, $currentYear, $privateInsurance) {
                        $previousYearValue = collect($employee->privateInsuranceValues)->where('year', $previousYear)->first();
                        $currentYearValue = collect($employee->privateInsuranceValues)->where('year', $currentYear)->first();

                        $currentPrivateInsurance = collect($employee->privateInsurances)->first();

                        $employee->current_private_insurance = $currentPrivateInsurance;

                        $employee->previous_year_value = [
                            'total_premi' => $previousYearValue->total_premi ?? 0,
                            'kesehatan' => $previousYearValue->kesehatan ?? 0,
                            'nilai_tabungan' => $previousYearValue->nilai_tabungan ?? 0,
                        ];
                        $employee->current_year_value = [
                            'total_premi' => $currentYearValue->total_premi ?? 0,
                            'kesehatan' => $currentYearValue->kesehatan ?? 0,
                            'premi_tabungan' => $privateInsurance->premi_tabungan ?? 0,
                            'nilai_tabungan' => $currentYearValue->nilai_tabungan ?? 0,
                            'premi_kematian' => $privateInsurance->premi_kematian ?? 0,
                        ];
                    });

                    $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];

                    $subtotals = [];

                    foreach ($properties as $property) {
                        $total = collect($division->employees)->sum(function ($employee) use ($property) {
                            return $employee->current_year_value[$property] ?? 0;
                        });
                        $subtotals[$property] = $total;
                    }

                    $division->subtotal = $subtotals;
                });

            $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];

            $grandtotals = [];

            foreach ($properties as $property) {
                $total = collect($divisions)->sum(function ($division) use ($property) {
                    return $division->subtotal[$property] ?? 0;
                });
                // array_push($subtotals, [
                //     $property => $total,
                // ]);
                $grandtotals[$property] = $total;
            }

            $company = Company::find($companyId);

            return Excel::download(new PrivateInsurancesExport($divisions, $year, $company, $grandtotals, $privateInsurance), 'Rekap ' . ($privateInsurance->name ?? '[NAMA ASURANSI]') . ' - Periode Thn ' . $year . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * BPJS Ketenagakerjaan
     */
    public function insurancesList()
    {
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $year = request()->query('year');

        if (!isset($year)) {
            $year = date('Y');
        }

        $previousYear = ((int) $year) - 1;
        $currentYear = $year;

        // return $diffDays;

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
        // $divisions = Division::all();
        // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
        $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear) {
            $q->with([
                'bpjs',
                'bpjsValues' => function ($q2) use ($previousYear, $currentYear) {
                    // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                    $q2->where('year', $currentYear)->orderBy('id', 'desc');
                },
                'privateInsurances' => function ($q2) use ($currentYear) {
                    // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                    $q2->with(['values' => function ($q3) use ($currentYear) {
                        $q3->where('year', $currentYear)->orderBy('id', 'desc');
                    }]);
                },
                // 'privateInsuranceValues' => function ($q) use ($previousYear, $currentYear) {
                //     // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                //     $q->where('year', $currentYear)->orderBy('id', 'desc');
                // }
            ]);
        }]);

        if (isset($divisionId)) {
            $divisionsQuery->where('id', $divisionId);
        }

        if (isset($companyId)) {
            $divisionsQuery->where('company_id', $companyId);
        }

        $divisions = $divisionsQuery
            ->get()
            ->lazy()
            ->each(function ($division) use ($previousYear, $currentYear) {
                collect($division->employees)->each(function ($employee) use ($previousYear, $currentYear) {
                    // BPJS
                    $currentYearBpjsValue = collect($employee->bpjsValues)->where('year', $currentYear)->first();

                    $insuranceValues = [];

                    if (isset($employee->bpjs->ketenagakerjaan_number)) {
                        array_push($insuranceValues, [
                            'number' => $employee->bpjs->ketenagakerjaan_number ?? '',
                            'start_year' => $employee->bpjs->ketenagakerjaan_start_year ?? '',
                            'name' => 'BPJS Ketenagakerjaan',
                            'value' => 990000,
                        ]);
                    }

                    if (isset($employee->bpjs->mandiri_number)) {
                        array_push($insuranceValues, [
                            'number' => $employee->bpjs->mandiri_number ?? '',
                            'start_year' => '',
                            'name' => 'BPJS Mandiri',
                            'value' => 0,
                        ]);
                    }

                    // $currentPrivateInsurance = collect($employee->privateInsurances)->first();

                    // $employee->current_private_insurance = $currentPrivateInsurance;

                    // $employee->current_year_value = [
                    //     'total_premi' => $currentYearValue->total_premi ?? 0,
                    //     'kesehatan' => $currentYearValue->kesehatan ?? 0,
                    //     'premi_tabungan' => $privateInsurance->premi_tabungan ?? 0,
                    //     'nilai_tabungan' => $currentYearValue->nilai_tabungan ?? 0,
                    //     'premi_kematian' => $privateInsurance->premi_kematian ?? 0,
                    // ];

                    collect($employee->privateInsurances)->each(function ($privateInsurance) use (&$insuranceValues, $currentYear) {
                        $currentYearValue = collect($privateInsurance->values)->where('year', $currentYear)->first();

                        array_push($insuranceValues, [
                            'number' => $privateInsurance->pivot->number ?? '',
                            'start_year' => $privateInsurance->pivot->start_year ?? '',
                            'name' => $privateInsurance->name ?? '',
                            'value' => 123000,
                        ]);
                    });

                    $employee->insurance_values = $insuranceValues;
                    $employee->insurance_values_total = collect($insuranceValues)->sum('value');
                });

                // $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

                $subtotal = collect($division->employees)->sum('insurance_values_total');
                $division->subtotal = $subtotal;
            });

        // $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

        $grandtotal = collect($divisions)->sum('subtotal');

        // foreach ($properties as $property) {
        //     $total = collect($divisions)->sum(function ($division) use ($property) {
        //         return $division->subtotal[$property] ?? 0;
        //     });
        //     $grandtotals[$property] = $total;
        // }


        // return $divisions;
        $divisionsOptions = Division::all();

        return view('reports.insurances.insurances-list', [
            'companies' => $companies,
            'divisions' => $divisions,
            'divisions_options' => $divisionsOptions,
            'grand_total' => $grandtotal,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'year' => $year,
            ]
        ]);
    }

    /**
     * BPJS Ketenagakerjaan
     */
    public function insurancesListExport()
    {
        try {
            $companyId = request()->query('company');
            $divisionId = request()->query('division');
            $year = request()->query('year');

            if (!isset($year)) {
                $year = date('Y');
            }

            $previousYear = ((int) $year) - 1;
            $currentYear = $year;

            // return $diffDays;

            $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();
            // $divisions = Division::all();
            // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
            $divisionsQuery = Division::with(['company', 'employees' => function ($q) use ($previousYear, $currentYear) {
                $q->with([
                    'bpjs',
                    'bpjsValues' => function ($q2) use ($previousYear, $currentYear) {
                        // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                        $q2->where('year', $currentYear)->orderBy('id', 'desc');
                    },
                    'privateInsurances' => function ($q2) use ($currentYear) {
                        // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                        $q2->with(['values' => function ($q3) use ($currentYear) {
                            $q3->where('year', $currentYear)->orderBy('id', 'desc');
                        }]);
                    },
                    // 'privateInsuranceValues' => function ($q) use ($previousYear, $currentYear) {
                    //     // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                    //     $q->where('year', $currentYear)->orderBy('id', 'desc');
                    // }
                ]);
            }]);

            if (isset($divisionId)) {
                $divisionsQuery->where('id', $divisionId);
            }

            if (isset($companyId)) {
                $divisionsQuery->where('company_id', $companyId);
            }

            $divisions = $divisionsQuery
                ->get()
                ->lazy()
                ->each(function ($division) use ($previousYear, $currentYear) {
                    collect($division->employees)->each(function ($employee) use ($previousYear, $currentYear) {
                        // BPJS
                        $currentYearBpjsValue = collect($employee->bpjsValues)->where('year', $currentYear)->first();

                        $insuranceValues = [];

                        if (isset($employee->bpjs->ketenagakerjaan_number)) {
                            array_push($insuranceValues, [
                                'number' => $employee->bpjs->ketenagakerjaan_number ?? '',
                                'start_year' => $employee->bpjs->ketenagakerjaan_start_year ?? '',
                                'name' => 'BPJS Ketenagakerjaan',
                                'value' => 990000,
                            ]);
                        }

                        if (isset($employee->bpjs->mandiri_number)) {
                            array_push($insuranceValues, [
                                'number' => $employee->bpjs->mandiri_number ?? '',
                                'start_year' => '',
                                'name' => 'BPJS Mandiri',
                                'value' => 0,
                            ]);
                        }

                        collect($employee->privateInsurances)->each(function ($privateInsurance) use (&$insuranceValues, $currentYear) {
                            $currentYearValue = collect($privateInsurance->values)->where('year', $currentYear)->first();

                            array_push($insuranceValues, [
                                'number' => $privateInsurance->pivot->number ?? '',
                                'start_year' => $privateInsurance->pivot->start_year ?? '',
                                'name' => $privateInsurance->name ?? '',
                                'value' => 123000,
                            ]);
                        });

                        $employee->insurance_values = $insuranceValues;
                        $employee->insurance_values_total = collect($insuranceValues)->sum('value');
                    });

                    // $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

                    $subtotal = collect($division->employees)->sum('insurance_values_total');
                    $division->subtotal = $subtotal;
                });

            // $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];

            $grandtotal = collect($divisions)->sum('subtotal');

            // return $divisions;
            $company = Company::find($companyId);

            return Excel::download(new InsurancesListExport($divisions, $year, $company, $grandtotal), 'Daftar Asuransi Periode Thn ' . $year . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    private function depositResources()
    {
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $startDate = request()->query('start_date');
        $endDate = request()->query('end_date');
        $status = request()->query('status');
        if (!isset($startDate) || !isset($endDate)) {
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-t');
        }

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();

        $depositsQuery = SalaryDeposit::query()
            ->with(['employee', 'items'])
            ->withSum(['items' => function ($q) {
                $q->where('paid', 1);
            }], 'amount')
            ->withCount(['items as paid_items_count' => function ($q) {
                $q->where('paid', 1);
            }]);

        if (isset($companyId)) {
            $depositsQuery->whereHas('employee', function ($q) use ($companyId) {
                $q->whereHas('office.division.company', function ($q2) use ($companyId) {
                    $q2->where('id', $companyId);
                });
            });
        }

        if (isset($divisionId)) {
            $depositsQuery->whereHas('employee', function ($q) use ($divisionId) {
                $q->whereHas('office.division', function ($q2) use ($divisionId) {
                    $q2->where('id', $divisionId);
                });
            });
        }

        if (isset($status)) {
            $depositsQuery->where('redeemed', $status);
        }

        $deposits = $depositsQuery->whereBetween('date', [$startDate, $endDate])->get();

        $divisionsOptions = Division::all();

        $company = Company::find($companyId);

        return [
            'companies' => $companies,
            'company' => $company,
            // 'divisions' => $divisions,
            'deposits' => $deposits,
            'divisions_options' => $divisionsOptions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
            ],
        ];
    }

    /**
     * Deposit list
     */
    public function deposits()
    {
        // return $this->depositResources();
        return view('reports.salaries.deposits', collect($this->depositResources())->merge([
            'other' => [],
        ])->all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function depositsExport()
    {
        try {
            $resources = $this->depositResources();
            $deposits = $resources['deposits'];
            $startDate = $resources['filter']['start_date'];
            $endDate = $resources['filter']['end_date'];
            $company = $resources['company'];

            return Excel::download(new DepositExport($deposits, $startDate, $endDate, $company), 'Laporan Deposit ' . $startDate . ' - ' . $endDate . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    private function leavePayrollResources()
    {
        $companyId = request()->query('company');
        $divisionId = request()->query('division');

        $companies = Company::with(['presidentDirector', 'director', 'commisioner'])->get();

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

        if (isset($companyId)) {
            $employeesWithleave->whereHas('office.division.company', function ($q2) use ($companyId) {
                $q2->where('id', $companyId);
            });
        }

        if (isset($divisionId)) {
            $employeesWithleave->whereHas('office.division', function ($q2) use ($divisionId) {
                $q2->where('id', $divisionId);
            });
        }

        $currentDate = date('Y-m-d');

        $employeesWithleave = $employeesWithleave->with([
            'salaryComponents' => function ($q) use ($currentDate) {
                $q->wherePivot('effective_date', '<=', $currentDate)->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
            },
            'leaveApplications' => function ($q) use ($year) {
                $q->whereHas('category', function ($q2) {
                    $q2->where('type', 'annual_leave');
                })
                    ->where('approval_status', 'approved')
                    ->where('application_dates', 'like', '%' . $year . '%');
            },
            'activeCareer.jobTitle',
            'leave',
            'office.division.company'
        ])
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

                $employee->grouped_leave_applications = $monthLeaveApplications;

                $leaveApplicationsDates = collect($employee->leaveApplications)->pluck('application_dates')->flatMap(function ($leaveApplicationDates) {
                    return explode(',', $leaveApplicationDates);
                })->all();

                $leaveApplicationsUniqueDates = array_unique($leaveApplicationsDates);

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

                // Leave Payroll
                $salaryComponents = $employee->salaryComponents;
                $gajiPokokAmount = collect($salaryComponents)->where('salary_type', 'gaji_pokok')->first()->pivot->amount ?? 0;
                $dailyWageAmount = collect($salaryComponents)->where('salary_type', 'uang_harian')->first()->pivot->amount ?? 0;

                $finalDailyWageAmount = $dailyWageAmount * 26;
                $totalSalary = ($gajiPokokAmount + $finalDailyWageAmount) / 26;
                $remainingLeave = ($leave['total'] ?? 0) - ($leave['taken'] ?? 0);

                $redeemedLeaveAmount = $remainingLeave * $totalSalary;

                if ($redeemedLeaveAmount < 0) {
                    $redeemedLeaveAmount = 0;
                }

                $employee->gaji_pokok = $gajiPokokAmount;
                $employee->uang_harian = $dailyWageAmount;
                $employee->redeemed_leave_amount = $redeemedLeaveAmount;

                return $employee;
            })->all();

        $divisionsOptions = Division::all();

        $company = Company::find($companyId);

        return [
            'companies' => $companies,
            'company' => $company,
            // 'divisions' => $divisions,
            'employees' => $employeesWithleave,
            'divisions_options' => $divisionsOptions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'year' => $year,
            ],
        ];
    }

    /**
     * Deposit list
     */
    public function leavePayroll()
    {
        // return $this->depositResources();
        return view('reports.salaries.leaves', collect($this->leavePayrollResources())->merge([
            'other' => [],
        ])->all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function leavePayrollExport()
    {
        try {
            $resources = $this->leavePayrollResources();
            $employees = collect($resources['employees'])->groupBy(function ($employee) {
                return $employee->office->division->company_id ?? 0;
            })->all();
            // return $employees;
            $year = $resources['filter']['year'];
            $company = $resources['company'];

            return Excel::download(new LeavePayrollExport($employees, $year, $company), 'Laporan Cuti Diuangkan Tahun ' . $year . '.xlsx');
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
}
