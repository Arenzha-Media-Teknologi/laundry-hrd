<?php

namespace App\Exports\salaries;

use App\Exports\salaries\Sheets\SalaryMonthlySheet;
use App\Exports\salaries\Sheets\SalaryMonthlySheetV2;
use App\Exports\salaries\Sheets\SummarySalaryMonthlySheet;
use App\Exports\salaries\Sheets\SummarySalaryMonthlySheetV2;
use App\Models\Company;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalaryMonthlyExportV2 implements WithMultipleSheets, ShouldAutoSize
{
    use Exportable;

    protected $startDatePeriod;
    protected $endDatePeriod;
    protected $staffOnly;
    protected $haveStaffPermission;
    protected $companyId;
    protected $presenceIncentiveOnly;

    public function __construct($startDatePeriod, $endDatePeriod, $staffOnly, $haveStaffPermission, $companyId, $presenceIncentiveOnly)
    {
        $this->startDatePeriod = $startDatePeriod;
        $this->endDatePeriod = $endDatePeriod;
        $this->staffOnly = $staffOnly;
        $this->haveStaffPermission = $haveStaffPermission;
        $this->companyId = $companyId;
        $this->presenceIncentiveOnly = $presenceIncentiveOnly;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // $companyInitials = ['MM', 'UL', 'SRC', 'BIS', 'EO', 'OELLO'];
        $companies = Company::where('id', $this->companyId)->get()->all();

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
                $lateFee = 0;
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
                    // if ($employee->type == "staff") {
                    //     $attendanceAllowance = 0;
                    // }

                    $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
                    $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
                    $lateFee = $salaryItems->where('salary_type', 'late')->sum('amount');

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
                $employee->late_fee = $lateFee;
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
            // ->each(function ($employee) {
            //     if ($employee->type == "staff") {
            //         $employee->attendance_allowance = 0;
            //     }
            // });

            $finalEmployeesByCompany = $employeesByCompany->all();

            $companyTotal = collect($employeesByCompany)->each(function ($employee) {
                // if ($employee->type == "staff") {
                //     $employee->attendance_allowance = 0;
                // }
            })->sum('total');

            $companyStaffAttendanceAllowanceTotal = collect($employeesByCompany)->where('type', 'staff')->sum('attendance_allowance');

            array_push($summariesByCompany, [
                'company' => $company,
                'total' => $companyTotal - $companyStaffAttendanceAllowanceTotal,
            ]);

            $sheets[] = new SalaryMonthlySheetV2($company, $finalEmployeesByCompany, $this->presenceIncentiveOnly);
        }

        if ($this->presenceIncentiveOnly != 1) {
            $sheets[] = new SummarySalaryMonthlySheetV2($summariesByCompany);
        }

        return $sheets;
    }
}
