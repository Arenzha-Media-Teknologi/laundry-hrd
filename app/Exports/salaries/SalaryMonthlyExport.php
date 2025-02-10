<?php

namespace App\Exports\salaries;

use App\Exports\salaries\Sheets\SalaryMonthlySheet;
use App\Exports\salaries\Sheets\SummarySalaryMonthlySheet;
use App\Models\Company;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalaryMonthlyExport implements WithMultipleSheets, ShouldAutoSize
{
    use Exportable;

    protected $startDatePeriod;
    protected $endDatePeriod;
    protected $staffOnly;
    protected $haveStaffPermission;

    public function __construct($startDatePeriod, $endDatePeriod, $staffOnly, $haveStaffPermission)
    {
        $this->startDatePeriod = $startDatePeriod;
        $this->endDatePeriod = $endDatePeriod;
        $this->staffOnly = $staffOnly;
        $this->haveStaffPermission = $haveStaffPermission;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // $companyInitials = ['MM', 'UL', 'SRC', 'BIS', 'EO', 'OELLO'];
        $companies = Company::all()->push(null)->all();

        $startDate = $this->startDatePeriod;
        $endDate = $this->endDatePeriod;
        $staffOnly = $this->staffOnly;
        $haveStaffPermission = $this->haveStaffPermission;

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

        $employees = $employees->get()
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

                $employee->basic_salary = $basicSalary;
                $employee->position_allowance = $positionAllowance;
                $employee->attendance_allowance = $attendanceAllowance;
                $employee->loan = $currentMonthLoans;
                $employee->total_loan = $totalLoan;
                $employee->total_paid_loan = $totalPaidLoan;
                $employee->loan_balance = $remainingLoan;
                $employee->excess_leave = $excessLeave;
                $employee->total = $total;
                $employee->bank_account_number = $bankAccountNumber;

                return $employee;
            });

        $summariesByCompany = [];

        foreach ($companies as $company) {
            $sheetCompanyId = null;
            if (isset($company->id)) {
                $sheetCompanyId = $company->id;
            }

            $employeesByCompany = $employees
                ->filter(
                    function ($employee) use ($sheetCompanyId) {
                        $employeeCompanyId = null;
                        if (isset($employee?->office?->division?->company?->id)) {
                            $employeeCompanyId = $employee?->office?->division?->company?->id;
                        }

                        return $employeeCompanyId == $sheetCompanyId;
                    }
                );

            $finalEmployeesByCompany = $employeesByCompany->all();

            $companyTotal = collect($employeesByCompany)->sum('total');

            array_push($summariesByCompany, [
                'company' => $company,
                'total' => $companyTotal,
            ]);

            $sheets[] = new SalaryMonthlySheet($company, $finalEmployeesByCompany);
        }

        $sheets[] = new SummarySalaryMonthlySheet($summariesByCompany);

        return $sheets;
    }
}
